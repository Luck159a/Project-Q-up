<?php

namespace App\Events;

use App\Models\Queue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ยิง Event นี้ทุกครั้งที่เจ้าหน้าที่กด "เรียกคิว" (status -> กำลังใช้บริการ)
 * ฝั่ง Frontend (จอมอนิเตอร์ / หน้าจอผู้ป่วย) subscribe ช่อง "doctor-schedule.{id}" ผ่าน Laravel Echo
 * เพื่ออัปเดตหน้าจอทันทีโดยไม่ต้อง refresh
 *
 * ต้องติดตั้ง Laravel Reverb ก่อนใช้งานจริง:
 *   composer require laravel/reverb
 *   php artisan reverb:install
 *   BROADCAST_CONNECTION=reverb ใน .env
 */
class QueueCalled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Queue $queue) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('doctor-schedule.'.$this->queue->docschId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'queue.called';
    }

    public function broadcastWith(): array
    {
        return [
            'queue_id' => $this->queue->id,
            'label_no' => $this->queue->labelNo,
            'period' => $this->queue->period,
            'status' => $this->queue->status,
        ];
    }
}