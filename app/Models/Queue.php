<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    use HasFactory;

    // 1. กำหนด Status Constants
    public const STATUS_WAITING = 'waiting';
    public const STATUS_IN_SERVICE = 'in_service';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // 2. กำหนด ACTIVE_STATUSES (สถานะที่ถือว่าจองคิวไว้และยังใช้อยู่)
    public const ACTIVE_STATUSES = [
        self::STATUS_WAITING,
        self::STATUS_IN_SERVICE,
        self::STATUS_COMPLETED,
    ];

    protected $fillable = [
        'userId',
        'doctor_schedule_id',
        'docschId', // เพิ่มเผื่อไว้ถ้าใน DB ใช้ชื่อนี้
        'queue_number',
        'period',
        'status',
        'symptoms',
    ];

    /**
     * ความสัมพันธ์กับ User (แก้ปัญหา Call to undefined method Queue::user)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    /**
     * ความสัมพันธ์กับ DoctorSchedule
     */
    public function doctorSchedule(): BelongsTo
    {
        // เช็ก Foreign Key ให้ตรงกับตารางของคุณ ( doctor_schedule_id หรือ docschId )
        return $this->belongsTo(DoctorSchedule::class, 'docschId'); 
    }
}