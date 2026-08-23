<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// --- หน้าแรก ---
Route::get('/', function () {
    return view('welcome');
});

// --- Redirect /dashboard ตาม Role ผู้ใช้ ---
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if (strtolower($user->role) === 'patient') {
            return view('dashboard');
        }

        return view('dashboard-admin');
    })->name('dashboard');
});

// --- กลุ่ม Route สำหรับผู้ที่ล็อกอินแล้ว ---
Route::middleware('auth')->group(function () {

    // โปรไฟล์
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // การจองคิว
    Route::get('/queue/book', [QueueController::class, 'book'])->name('queue.book');
    Route::get('/queue/book/{scheduleId}', [QueueController::class, 'create'])->name('queues.create');
    Route::post('/queue/book', [QueueController::class, 'store'])->name('queues.store');
    Route::get('/queue/success/{id}', [QueueController::class, 'success'])->name('queue.success');
    Route::get('/queue/history', [QueueController::class, 'history'])->name('queue.history');
    Route::get('/queues/pdf/{id}', [QueueController::class, 'exportTicketPDF'])->name('queues.pdf');
    Route::patch('/queues/{id}/cancel', [QueueController::class, 'cancel'])->name('queues.cancel');

    // ------------------------------------------------------------------------
    // 🌟 จัดการ Route ของ doctor-schedules (เรียงลำดับใหม่)
    // ------------------------------------------------------------------------

    // 1. หน้าแสดงตารางรวม (เข้าได้ทุกคน)
    Route::get('/doctor-schedules', [DoctorScheduleController::class, 'index'])->name('doctor-schedules.index');

    // 2. หน้าสร้างตารางหมอ (ต้องวางไว้ก่อน {doctorSchedule} เสมอ)
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/doctor-schedules/create', [DoctorScheduleController::class, 'create'])->name('doctor-schedules.create');
        Route::post('/doctor-schedules', [DoctorScheduleController::class, 'store'])->name('doctor-schedules.store');
    });

    // 3. หน้าแสดงรายละเอียดแบบรายบุคคล (เข้าได้ทุกคน)
    Route::get('/doctor-schedules/{doctorSchedule}', [DoctorScheduleController::class, 'show'])->name('doctor-schedules.show');

    // 4. แก้ไข และ ลบ ( Admin / Staff )
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/doctor-schedules/{doctorSchedule}/edit', [DoctorScheduleController::class, 'edit'])->name('doctor-schedules.edit');
        Route::put('/doctor-schedules/{doctorSchedule}', [DoctorScheduleController::class, 'update'])->name('doctor-schedules.update');
        Route::patch('/doctor-schedules/{doctorSchedule}', [DoctorScheduleController::class, 'update']);
        Route::delete('/doctor-schedules/{doctorSchedule}', [DoctorScheduleController::class, 'destroy'])->name('doctor-schedules.destroy');

        // จัดการคิว
        Route::get('/queues', [QueueController::class, 'index'])->name('queues.index');
        Route::patch('/queues/{id}/status', [QueueController::class, 'updateStatus'])->name('queues.updateStatus');
        
        // รายงาน PDF
        Route::get('/queues/export-pdf', [QueueController::class, 'exportPDF'])->name('queues.export-pdf');
        Route::get('/admin/queues/export-pdf', [QueueController::class, 'exportPDF'])->name('admin.queues.export-pdf');
        Route::get('/report/daily', [ReportController::class, 'daily'])->name('report.daily');
        Route::get('/admin/report/services/pdf', [ReportController::class, 'daily'])->name('report.service.pdf');
        Route::get('/admin/users/export-pdf', [UserController::class, 'exportPDF'])->name('admin.users.export-pdf');
        Route::get('/reports/users/pdf', [ReportController::class, 'exportUsersPdf'])->name('reports.users.pdf');
    });

    // --- หน้าคิวของหมอวันนี้ ---
    Route::middleware('role:doctor')->group(function () {
        Route::get('/doctor/queue', [QueueController::class, 'book'])->name('doctor.queue.list');
    });

    // --- จัดการผู้ใช้งาน: Admin เท่านั้น ---
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';