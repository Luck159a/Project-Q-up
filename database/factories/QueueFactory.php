<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\DoctorSchedule; // หรือ Model ตารางตารางตรวจหมอของคุณ
use Illuminate\Database\Eloquent\Factories\Factory;

class QueueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'userId' => User::factory(),
            'docschId' => DoctorSchedule::factory(), // <-- บรรทัดนี้สำคัญสุด เพื่อให้ docschId ไม่เป็น NULL
            'period' => '09:00 - 09:20',
            'status' => 'waiting',
        ];
    }
}