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

    // การจองคิว (สำหรับ คนไข้/ผู้ใช้ทั่วไป)
    Route::get('/queue/book', [QueueController::class, 'book'])->name('queue.book');
    Route::get('/queue/book/{scheduleId}', [QueueController::class, 'create'])->name('queues.create');
    Route::post('/queue/book', [QueueController::class, 'store'])->name('queues.store');
    Route::get('/queue/success/{id}', [QueueController::class, 'success'])->name('queue.success');
    Route::get('/queue/history', [QueueController::class, 'history'])->name('queue.history');
    Route::get('/queues/pdf/{id}', [QueueController::class, 'exportTicketPDF'])->name('queues.pdf');
    Route::patch('/queues/{id}/cancel', [QueueController::class, 'cancel'])->name('queues.cancel');

    // ------------------------------------------------------------------------
    // Shared Access (Admin, Staff & Doctor) - ดูคิวและเปลี่ยนสถานะคิว
    // ------------------------------------------------------------------------
    Route::middleware('role:admin,staff,doctor')->group(function () {
        Route::get('/queues', [QueueController::class, 'index'])->name('queues.index');
        Route::patch('/queues/{id}/status', [QueueController::class, 'updateStatus'])->name('queues.updateStatus');
    });

    // ------------------------------------------------------------------------
    // Staff & Admin Access
    // ------------------------------------------------------------------------
    Route::middleware('role:admin,staff')->group(function () {
        // จัดการตารางหมอ (วางเส้นทาง static ไว้ก่อน parameter)
        Route::get('/doctor-schedules/create', [DoctorScheduleController::class, 'create'])->name('doctor-schedules.create');
        Route::post('/doctor-schedules', [DoctorScheduleController::class, 'store'])->name('doctor-schedules.store');
        Route::get('/doctor-schedules/{doctorSchedule}/edit', [DoctorScheduleController::class, 'edit'])->name('doctor-schedules.edit');
        Route::put('/doctor-schedules/{doctorSchedule}', [DoctorScheduleController::class, 'update'])->name('doctor-schedules.update');
        Route::delete('/doctor-schedules/{doctorSchedule}', [DoctorScheduleController::class, 'destroy'])->name('doctor-schedules.destroy');

        // จัดการและออกรายงานคิว
        Route::get('/queues/export-pdf', [QueueController::class, 'exportPDF'])->name('queues.export-pdf');
        Route::get('/admin/queues/export-pdf', [QueueController::class, 'exportPDF'])->name('admin.queues.export-pdf');
        
        // รายงานระบบ
        Route::get('/report/daily', [ReportController::class, 'daily'])->name('report.daily');
        Route::get('/reports/users/pdf', [ReportController::class, 'exportUsersPdf'])->name('reports.users.pdf');
    });

    // ตารางเวลาหมอ (เข้าชมได้ทุกคน)
    Route::get('/doctor-schedules', [DoctorScheduleController::class, 'index'])->name('doctor-schedules.index');
    Route::get('/doctor-schedules/{doctorSchedule}', [DoctorScheduleController::class, 'show'])->name('doctor-schedules.show');

    // --- สำหรับหมอเท่านั้น ---
    Route::middleware('role:doctor')->group(function () {
        Route::get('/doctor/queue', [QueueController::class, 'doctorToday'])->name('doctor.queue.list');
        // เพิ่มรองรับชื่อ queue.doctor-today เพื่อให้ Blade หน้าเก่า/เมนูเดิมทำงานได้ไม่เกิด RouteNotFoundException
        Route::get('/doctor/today-queue', [QueueController::class, 'doctorToday'])->name('queue.doctor-today');
    });

    // --- จัดการผู้ใช้งาน: Admin เท่านั้น ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users/export-pdf', [UserController::class, 'exportPDF'])->name('admin.users.export-pdf');
        Route::resource('users', UserController::class);
    });
});

require __DIR__.'/auth.php';