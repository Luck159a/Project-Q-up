<?php

use App\Models\DoctorSchedule;
use App\Models\Queue;
use App\Models\User;
use App\Services\QueueService;
use Illuminate\Validation\ValidationException;

function bookedQueue(): Queue
{
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    $schedule = DoctorSchedule::create([
        'user_id' => $doctor->id,
        'schedule_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '12:00',
        'status' => 'available',
    ]);

    return app(QueueService::class)->bookQueue($schedule->id, '09:00 - 09:20', null, $patient, $patient);
}

test('staff can move a queue from waiting to in-service to completed in order', function () {
    $queue = bookedQueue();
    $staff = User::factory()->staff()->create();
    $service = app(QueueService::class);

    $queue = $service->changeStatus($queue->id, Queue::STATUS_IN_SERVICE, $staff);
    expect($queue->status)->toBe(Queue::STATUS_IN_SERVICE);

    $queue = $service->changeStatus($queue->id, Queue::STATUS_COMPLETED, $staff);
    expect($queue->status)->toBe(Queue::STATUS_COMPLETED);

    expect($queue->statusLogs()->count())->toBe(3); // create + 2 transitions
});

test('a queue cannot skip straight from waiting to completed', function () {
    $queue = bookedQueue();
    $staff = User::factory()->staff()->create();

    expect(fn () => app(QueueService::class)->changeStatus($queue->id, Queue::STATUS_COMPLETED, $staff))
        ->toThrow(ValidationException::class);
});

test('a completed queue cannot be reopened or cancelled', function () {
    $queue = bookedQueue();
    $staff = User::factory()->staff()->create();
    $service = app(QueueService::class);

    $service->changeStatus($queue->id, Queue::STATUS_IN_SERVICE, $staff);
    $service->changeStatus($queue->id, Queue::STATUS_COMPLETED, $staff);

    expect(fn () => $service->changeStatus($queue->id, Queue::STATUS_CANCELLED, $staff))
        ->toThrow(ValidationException::class);
});

test('every status change is written to the audit log with who changed it', function () {
    $queue = bookedQueue();
    $staff = User::factory()->staff()->create();

    app(QueueService::class)->changeStatus($queue->id, Queue::STATUS_IN_SERVICE, $staff, 'เรียกคิวจากหน้าเคาน์เตอร์');

    $log = $queue->statusLogs()->first();

    expect($log->from_status)->toBe(Queue::STATUS_WAITING)
        ->and($log->to_status)->toBe(Queue::STATUS_IN_SERVICE)
        ->and($log->changed_by)->toBe($staff->id)
        ->and($log->note)->toBe('เรียกคิวจากหน้าเคาน์เตอร์');
}); 