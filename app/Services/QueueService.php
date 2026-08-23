<?php

namespace App\Services;

use App\Models\DoctorSchedule;
use App\Models\Queue;
use App\Models\QueueStatusLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QueueService
{
    public const SLOT_MINUTES = 20;

    public function buildSlots(DoctorSchedule $schedule): array
    {
        $bookedPeriods = $schedule->queues()
            ->whereIn('status', Queue::ACTIVE_STATUSES)
            ->pluck('period')
            ->all();

        $slots = [];
        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);

        while ($start->copy()->addMinutes(self::SLOT_MINUTES) <= $end) {
            $slotEnd = $start->copy()->addMinutes(self::SLOT_MINUTES);
            $timeRange = $start->format('H:i').' - '.$slotEnd->format('H:i');

            $slots[] = [
                'time' => $timeRange,
                'is_available' => ! in_array($timeRange, $bookedPeriods, true),
            ];

            $start->addMinutes(self::SLOT_MINUTES);
        }

        return $slots;
    }

    public function bookQueue(int $scheduleId, string $period, ?string $note, User $patient, User $actor): Queue
    {
        return DB::transaction(function () use ($scheduleId, $period, $note, $patient, $actor) {
            $schedule = DoctorSchedule::where('id', $scheduleId)->lockForUpdate()->first();

            if (! $schedule) {
                throw ValidationException::withMessages([
                    'docschId' => 'ไม่พบตารางเวลาที่เลือก',
                ]);
            }

            if ($schedule->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'docschId' => 'ตารางเวลานี้ถูกยกเลิกแล้ว ไม่สามารถจองได้',
                ]);
            }

            if (Carbon::parse($schedule->schedule_date)->lt(Carbon::today())) {
                throw ValidationException::withMessages([
                    'docschId' => 'ไม่สามารถจองคิวย้อนหลังได้',
                ]);
            }

            if (! $this->isPeriodWithinSchedule($schedule, $period)) {
                throw ValidationException::withMessages([
                    'period' => 'ช่วงเวลาที่เลือกไม่ตรงกับตารางออกตรวจของแพทย์',
                ]);
            }

            if ($patient->isPatient()) {
                $hasExistingActiveQueue = Queue::where('userId', $patient->id)
                    ->whereIn('status', Queue::ACTIVE_STATUSES)
                    ->whereHas('doctorSchedule', function ($query) use ($schedule) {
                        $query->where('schedule_date', $schedule->schedule_date);
                    })
                    ->exists();

                if ($hasExistingActiveQueue) {
                    throw ValidationException::withMessages([
                        'period' => 'คุณได้ทำการจองคิวสำหรับวันที่ '.Carbon::parse($schedule->schedule_date)->format('d/m/Y').' ไปแล้ว (จำกัดการจอง 1 คิวต่อวัน)',
                    ]);
                }
            }

            $isTimeSlotTaken = Queue::where('docschId', $schedule->id)
                ->where('period', trim($period))
                ->whereIn('status', Queue::ACTIVE_STATUSES)
                ->exists();

            if ($isTimeSlotTaken) {
                throw ValidationException::withMessages([
                    'period' => 'ขออภัย ช่วงเวลานี้ถูกจองไปแล้ว กรุณาเลือกช่วงเวลาอื่น',
                ]);
            }

            $queue = Queue::create([
                'userId' => $patient->id,
                'docschId' => $schedule->id,
                'period' => trim($period),
                'labelNo' => $this->generateQueueNumber($schedule),
                'Note' => $note,
                'status' => Queue::STATUS_WAITING,
                'created_by' => $actor->id,
            ]);

            $this->logStatusChange($queue, null, Queue::STATUS_WAITING, $actor, 'สร้างคิวใหม่');

            return $queue;
        });
    }

    public function changeStatus(int $queueId, string $newStatus, User $actor, ?string $note = null): Queue
    {
        return DB::transaction(function () use ($queueId, $newStatus, $actor, $note) {
            $queue = Queue::where('id', $queueId)->lockForUpdate()->first();

            if (! $queue) {
                throw ValidationException::withMessages(['status' => 'ไม่พบคิวนี้']);
            }

            $validStatuses = [
                Queue::STATUS_WAITING,
                Queue::STATUS_IN_SERVICE,
                Queue::STATUS_COMPLETED,
                Queue::STATUS_CANCELLED,
            ];

            if (! in_array($newStatus, $validStatuses, true)) {
                throw ValidationException::withMessages(['status' => 'สถานะไม่ถูกต้อง']);
            }

            $this->authorizeTransition($queue, $newStatus, $actor);

            $previousStatus = $queue->status;
            $queue->status = $newStatus;
            $queue->save();

            $this->logStatusChange($queue, $previousStatus, $newStatus, $actor, $note);

            return $queue;
        });
    }

    protected function authorizeTransition(Queue $queue, string $newStatus, User $actor): void
    {
        if ($newStatus === Queue::STATUS_CANCELLED) {
            $isOwner = $actor->id === $queue->userId;

            if (! $isOwner && ! $actor->isStaffOrAdmin()) {
                throw ValidationException::withMessages([
                    'status' => 'คุณไม่มีสิทธิ์ยกเลิกคิวนี้',
                ]);
            }

            return;
        }

        if (! $actor->isStaffOrAdmin()) {
            throw ValidationException::withMessages([
                'status' => 'คุณไม่มีสิทธิ์เปลี่ยนสถานะคิวนี้',
            ]);
        }
    }

    protected function logStatusChange(Queue $queue, ?string $from, string $to, User $actor, ?string $note = null): void
    {
        QueueStatusLog::create([
            'queue_id' => $queue->id,
            'from_status' => $from,
            'to_status' => $to,
            'changed_by' => $actor->id,
            'note' => $note,
        ]);
    }

    protected function generateQueueNumber(DoctorSchedule $schedule): string
    {
        $allDoctorIds = User::where('role', 'doctor')
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $doctorIndex = array_search($schedule->user_id, $allDoctorIds, true);
        $doctorLetter = $doctorIndex !== false ? chr(65 + $doctorIndex) : 'A';

        $lastQueue = Queue::where('docschId', $schedule->id)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($lastQueue && preg_match('/(\d+)$/', $lastQueue->labelNo, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return 'Q-'.$doctorLetter.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    protected function isPeriodWithinSchedule(DoctorSchedule $schedule, string $period): bool
    {
        $slots = collect($this->buildSlots($schedule))->pluck('time')->all();

        return in_array(trim($period), $slots, true);
    }
}