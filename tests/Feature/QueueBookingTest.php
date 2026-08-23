<?php

use App\Models\DoctorSchedule;
use App\Models\Queue;
use App\Models\User;
use App\Services\QueueService;
use Illuminate\Validation\ValidationException;

function makeSchedule(User $doctor, string $date = null): DoctorSchedule
{
    return DoctorSchedule::create([
        'user_id' => $doctor->id,
        'schedule_date' => $date ?? now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '12:00',
        'status' => 'available',
    ]);
}

test('a patient can book an open slot', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    $schedule = makeSchedule($doctor);

    $service = app(QueueService::class);
    $queue = $service->bookQueue($schedule->id, '09:00 - 09:20', 'ปวดหัว', $patient, $patient);

    expect($queue->status)->toBe(Queue::STATUS_WAITING)
        ->and($queue->labelNo)->toStartWith('Q-')
        ->and($queue->statusLogs()->count())->toBe(1);
});

test('booking the same time slot twice is rejected (prevents double-booking a slot)', function () {
    $doctor = User::factory()->doctor()->create();
    $patientA = User::factory()->patient()->create();
    $patientB = User::factory()->patient()->create();
    $schedule = makeSchedule($doctor);

    $service = app(QueueService::class);
    $service->bookQueue($schedule->id, '09:00 - 09:20', null, $patientA, $patientA);

    expect(fn () => $service->bookQueue($schedule->id, '09:00 - 09:20', null, $patientB, $patientB))
        ->toThrow(ValidationException::class);

    // ยืนยันว่ามีคิว "active" อยู่แค่รายการเดียวสำหรับช่วงเวลานี้ ไม่เกิดการจองซ้ำ
    expect(
        Queue::where('docschId', $schedule->id)
            ->where('period', '09:00 - 09:20')
            ->whereIn('status', Queue::ACTIVE_STATUSES)
            ->count()
    )->toBe(1);
});

test('a patient cannot book two active queues on the same day (duplicate active booking)', function () {
    $doctorA = User::factory()->doctor()->create();
    $doctorB = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();

    $date = now()->addDay()->toDateString();
    $scheduleA = makeSchedule($doctorA, $date);
    $scheduleB = makeSchedule($doctorB, $date);

    $service = app(QueueService::class);
    $service->bookQueue($scheduleA->id, '09:00 - 09:20', null, $patient, $patient);

    expect(fn () => $service->bookQueue($scheduleB->id, '09:00 - 09:20', null, $patient, $patient))
        ->toThrow(ValidationException::class);
});

test('booking a cancelled queue slot frees it up for someone else', function () {
    $doctor = User::factory()->doctor()->create();
    $patientA = User::factory()->patient()->create();
    $patientB = User::factory()->patient()->create();
    $schedule = makeSchedule($doctor);

    $service = app(QueueService::class);
    $first = $service->bookQueue($schedule->id, '09:00 - 09:20', null, $patientA, $patientA);
    $service->changeStatus($first->id, Queue::STATUS_CANCELLED, $patientA);

    $second = $service->bookQueue($schedule->id, '09:00 - 09:20', null, $patientB, $patientB);

    expect($second->status)->toBe(Queue::STATUS_WAITING);
});

test('booking a past-dated schedule is rejected', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    $schedule = makeSchedule($doctor, now()->subDay()->toDateString());

    $service = app(QueueService::class);

    expect(fn () => $service->bookQueue($schedule->id, '09:00 - 09:20', null, $patient, $patient))
        ->toThrow(ValidationException::class);
});

test('booking a period outside the doctor schedule hours is rejected', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    $schedule = makeSchedule($doctor);

    $service = app(QueueService::class);

    expect(fn () => $service->bookQueue($schedule->id, '23:00 - 23:20', null, $patient, $patient))
        ->toThrow(ValidationException::class);
});

/**
 * หมายเหตุเรื่อง Concurrency: PHPUnit/Pest รันเทสในโปรเซสเดียว กับ 1 DB connection
 * เพราะฉะนั้นจึงไม่สามารถจำลองสถานการณ์ "สอง request ยิงเข้ามาพร้อมกันจริงๆ" ได้ตรงๆ
 * เทสนี้ยืนยันพฤติกรรมที่สำคัญกว่า คือ: เมื่อช่วงเวลาถูกจองไปแล้ว (ไม่ว่าจะมาจาก
 * request ไหน) request ที่มาทีหลังต้องถูกปฏิเสธเสมอ — ซึ่งเป็นสิ่งที่ DB::transaction
 * + lockForUpdate() ใน QueueService::bookQueue รับประกันไว้บน MySQL จริงตอน production
 * (แถวของ DoctorSchedule ที่ถูก lock ไว้จะบังคับให้ request ที่สองต้องรอจนกว่า
 * transaction แรก commit ก่อน แล้วจึงเห็นข้อมูลล่าสุดและถูกปฏิเสธอย่างถูกต้อง)
 */
test('sequential booking attempts on the same slot only let the first one through', function () {
    $doctor = User::factory()->doctor()->create();
    $schedule = makeSchedule($doctor);
    $service = app(QueueService::class);

    $results = [];
    foreach (range(1, 5) as $i) {
        $patient = User::factory()->patient()->create();
        try {
            $service->bookQueue($schedule->id, '09:00 - 09:20', null, $patient, $patient);
            $results[] = 'ok';
        } catch (ValidationException) {
            $results[] = 'rejected';
        }
    }

    expect(array_count_values($results))->toBe(['ok' => 1, 'rejected' => 4]);
});