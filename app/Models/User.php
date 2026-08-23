<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Role Constants
    public const ROLE_PATIENT = 'patient';
    public const ROLE_DOCTOR = 'doctor';
    public const ROLE_STAFF = 'staff';
    public const ROLE_ADMIN = 'admin';

    // 🌟 เปลี่ยนชื่อสิทธิ์เป็นภาษาไทย (สำหรับแสดงใน Dropdown/Select)
    public const ROLES = [
        self::ROLE_ADMIN   => 'ผู้ดูแลระบบ (Admin)',
        self::ROLE_STAFF   => 'เจ้าหน้าที่ (Staff)',
        self::ROLE_DOCTOR  => 'แพทย์ (Doctor)',
        self::ROLE_PATIENT => 'คนไข้ (Patient)',
    ];

    // Status Constants
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    // 🌟 เปลี่ยนสถานะเป็นภาษาไทย
    public const STATUSES = [
        self::STATUS_ACTIVE   => 'ใช้งานปกติ (Active)',
        self::STATUS_INACTIVE => 'ระงับการใช้งาน (Inactive)',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    public function isPatient(): bool
    {
        return $this->role === self::ROLE_PATIENT;
    }

    public function isDoctor(): bool
    {
        return $this->role === self::ROLE_DOCTOR;
    }

    public function isStaff(): bool
    {
        return $this->role === self::ROLE_STAFF;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isStaffOrAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_STAFF, self::ROLE_ADMIN], true);
    }
    
    public function queues(): HasMany
    {
        return $this->hasMany(Queue::class, 'userId');
    }

    public function doctorSchedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'user_id');
    }
}