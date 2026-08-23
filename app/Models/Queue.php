<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    use HasFactory;

    public const STATUS_WAITING = 'waiting';
    public const STATUS_IN_SERVICE = 'in_service';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const ACTIVE_STATUSES = [
        self::STATUS_WAITING,
        self::STATUS_IN_SERVICE,
        self::STATUS_COMPLETED,
    ];

    protected $fillable = [
        'userId',
        'docschId', // ปรับจาก doctor_schedule_id เป็น docschId ให้ตรงกับ DB
        'queue_number',
        'period',
        'status',
        'symptoms',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function doctorSchedule(): BelongsTo
    {
        // ระบุ Foreign Key เป็น 'docschId' ให้ตรงกับคอลัมน์ในตาราง queues
        return $this->belongsTo(DoctorSchedule::class, 'docschId');
    }
}