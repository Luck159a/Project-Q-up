<?php

use Illuminate\Support\Facades\Broadcast;

// ช่อง 'doctor-schedule.{id}' เป็น public channel (ไม่ใช้ private/presence)
// เพราะจอมอนิเตอร์คิวในคลินิกไม่ต้อง login — ถ้าต้องการจำกัดสิทธิ์การฟังในอนาคต
// ให้เปลี่ยนเป็น PrivateChannel/PresenceChannel แล้วเพิ่ม authorize callback ที่นี่ เช่น:
//
// Broadcast::channel('doctor-schedule.{scheduleId}', function ($user, $scheduleId) {
//     return $user->isStaffOrAdmin() || $user->doctorSchedules()->whereKey($scheduleId)->exists();
// });