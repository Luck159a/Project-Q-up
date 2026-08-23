<?php

use App\Models\DoctorSchedule;
use App\Models\User;
use App\Services\QueueService;

test('a patient cannot view the staff queue management list', function () {
    $patient = User::factory()->patient()->create();

    $this->actingAs($patient)
        ->get(route('queues.index'))
        ->assertRedirect(route('queue.history'));
});

test('staff can view the queue management list', function () {
    $staff = User::factory()->staff()->create();

    $this->actingAs($staff)
        ->get(route('queues.index'))
        ->assertOk();
});

test('a patient cannot call or complete a queue via the status endpoint', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    $schedule = DoctorSchedule::create([
        'user_id' => $doctor->id,
        'schedule_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '12:00',
        'status' => 'available',
    ]);
    $queue = app(QueueService::class)->bookQueue($schedule->id, '09:00 - 09:20', null, $patient, $patient);

    // Route ทั้งหมดถูกครอบด้วย middleware 'role:admin,staff' ที่ /queues/{id}/status แล้ว
    $this->actingAs($patient)
        ->patch(route('queues.updateStatus', $queue->id), ['status' => 'กำลังใช้บริการ'])
        ->assertForbidden();
});

test('a patient can cancel their own queue but not someone else\'s', function () {
    $doctor = User::factory()->doctor()->create();
    $owner = User::factory()->patient()->create();
    $otherPatient = User::factory()->patient()->create();
    $schedule = DoctorSchedule::create([
        'user_id' => $doctor->id,
        'schedule_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00',
        'end_time' => '12:00',
        'status' => 'available',
    ]);
    $queue = app(QueueService::class)->bookQueue($schedule->id, '09:00 - 09:20', null, $owner, $owner);

    $this->actingAs($otherPatient)
        ->patch(route('queues.cancel', $queue->id))
        ->assertRedirect();
    expect($queue->fresh()->status)->not->toBe(\App\Models\Queue::STATUS_CANCELLED);

    $this->actingAs($owner)
        ->patch(route('queues.cancel', $queue->id))
        ->assertRedirect();
    expect($queue->fresh()->status)->toBe(\App\Models\Queue::STATUS_CANCELLED);
});

test('only an admin can reach user management routes', function () {
    $staff = User::factory()->staff()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($staff)->get(route('users.index'))->assertForbidden();
    $this->actingAs($admin)->get(route('users.index'))->assertOk();
});

test('guests are redirected to login for protected routes', function () {
    $this->get(route('queues.index'))->assertRedirect(route('login'));
    $this->get(route('users.index'))->assertRedirect(route('login'));
});