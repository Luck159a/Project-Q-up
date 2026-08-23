<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. ถ้าเป็น Patient ให้ส่งไปหน้า dashboard ของคนไข้ปกติ
        if ($user->isPatient()) {
            return view('dashboard');
        }

        // 2. สำหรับ Admin, Staff, Doctor ให้คำนวณสถิติแล้วส่งไปหน้า dashboard-admin
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $countByRole = fn (string $role) => User::whereRaw('LOWER(role) = ?', [strtolower($role)]);
        $queuesByCreatorRole = fn (string $role) => Queue::whereHas('user', function ($q) use ($role) {
            $q->whereRaw('LOWER(role) = ?', [strtolower($role)]);
        });

        $stats = [
            'daily' => [
                'doctor' => $countByRole('doctor')->whereDate('created_at', $today)->count(),
                'staff' => $countByRole('staff')->whereDate('created_at', $today)->count(),
                'patient' => $countByRole('patient')->whereDate('created_at', $today)->count(),

                'queues' => Queue::whereDate('created_at', $today)->count(),

                'queues_doctor' => $queuesByCreatorRole('doctor')->whereDate('created_at', $today)->count(),
                'queues_staff' => $queuesByCreatorRole('staff')->whereDate('created_at', $today)->count(),
                'queues_patient' => $queuesByCreatorRole('patient')->whereDate('created_at', $today)->count(),
            ],

            'monthly' => [
                'doctor' => $countByRole('doctor')->where('created_at', '>=', $startOfMonth)->count(),
                'staff' => $countByRole('staff')->where('created_at', '>=', $startOfMonth)->count(),
                'patient' => $countByRole('patient')->where('created_at', '>=', $startOfMonth)->count(),
            ],

            'total' => [
                'patient' => $countByRole('patient')->count(),
                'queues' => Queue::count(),
            ],
        ];

        return view('dashboard-admin', compact('stats'));
    }
}