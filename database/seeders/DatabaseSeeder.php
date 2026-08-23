<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. สร้าง Test User เดิมของคุณ
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // 2. เพิ่มข้อมูลคุณหมอ A, B, C ตามที่คุณต้องการ
        User::create([
            'name' => 'หมอ A',
            'email' => 'doctor_a@example.com',
            'password' => Hash::make('password'), // ใช้ Hash::make เป็นมาตรฐานความปลอดภัย
            'role' => 'doctor',
        ]);

        User::create([
            'name' => 'หมอ B',
            'email' => 'doctor_b@example.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        User::create([
            'name' => 'หมอ C',
            'email' => 'doctor_c@example.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);
    }
}