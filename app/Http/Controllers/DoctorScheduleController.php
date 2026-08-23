<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    private function resolveView(string $viewName): string
    {
        $kebabView = "doctor-schedules.{$viewName}";
        $snakeView = "doctor_schedules.{$viewName}";

        if (ViewFacade::exists($kebabView)) {
            return $kebabView;
        }

        if (ViewFacade::exists($snakeView)) {
            return $snakeView;
        }

        return $kebabView;
    }

    public function index(Request $request): View
    {
        $query = DoctorSchedule::with(['user', 'queues']);

        if ($request->filled('doctor_id')) {
            $query->where('user_id', $request->doctor_id);
        }

        if ($request->filled('date')) {
            $query->where('schedule_date', $request->date);
        } else {
            $query->where('schedule_date', '>=', Carbon::today()->toDateString());
        }

        $schedules = $query->orderBy('schedule_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate(15)
            ->appends($request->query());

        $doctors = User::where('role', 'doctor')->get();

        return view($this->resolveView('index'), compact('schedules', 'doctors'));
    }

    public function create(): View
    {
        $doctors = User::where('role', 'doctor')->get();

        return view($this->resolveView('create'), compact('doctors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'       => ['required', 'exists:users,id'],
            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time'    => ['required', 'date_format:H:i'],
            'end_time'      => ['required', 'date_format:H:i', 'after:start_time'],
            'note'          => ['nullable', 'string', 'max:255'],
        ], [
            'user_id.required'             => 'กรุณาเลือกแพทย์',
            'schedule_date.after_or_equal' => 'ไม่สามารถสร้างตารางออกตรวจย้อนหลังได้',
            'end_time.after'               => 'เวลาสิ้นสุดต้องมาหลังเวลาเริ่มตรวจเสมอ',
        ]);

        // ตรวจช่วงเวลาทับซ้อน (ยกเว้นสถานะ cancelled)
        $hasOverlap = DoctorSchedule::where('user_id', $validated['user_id'])
            ->where('schedule_date', $validated['schedule_date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($hasOverlap) {
            return back()->withInput()->withErrors([
                'schedule_date' => 'แพทย์ท่านนี้มีตารางออกตรวจในช่วงเวลาดังกล่าวอยู่แล้ว',
            ]);
        }

        DoctorSchedule::create([
            'user_id'       => $validated['user_id'],
            'schedule_date' => $validated['schedule_date'],
            'start_time'    => $validated['start_time'],
            'end_time'      => $validated['end_time'],
            'note'          => $validated['note'] ?? null,
            'status'        => 'available', // 🌟 ปรับเป็น available
        ]);

        return redirect()->route('doctor-schedules.index')
            ->with('success', 'เพิ่มตารางออกตรวจเรียบร้อยแล้ว');
    }

    public function show(DoctorSchedule $doctorSchedule): View
    {
        $doctorSchedule->load(['user', 'queues.user']);

        return view($this->resolveView('show'), [
            'schedule' => $doctorSchedule,
        ]);
    }

    public function edit(DoctorSchedule $doctorSchedule): View
    {
        $doctors = User::where('role', 'doctor')->get();

        return view($this->resolveView('edit'), [
            'schedule' => $doctorSchedule,
            'doctors'  => $doctors,
        ]);
    }

    public function update(Request $request, DoctorSchedule $doctorSchedule): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'       => ['required', 'exists:users,id'],
            'schedule_date' => ['required', 'date'],
            'start_time'    => ['required', 'date_format:H:i'],
            'end_time'      => ['required', 'date_format:H:i', 'after:start_time'],
            'status'        => ['required', 'in:available,booked,cancelled'], // 🌟 ปรับ Validation ตาม Enum
            'note'          => ['nullable', 'string', 'max:255'],
        ]);

        // เช็ก Overlap ตอนแก้ไข (ถ้าสถานะไม่ใช่ cancelled)
        if ($validated['status'] !== 'cancelled') {
            $hasOverlap = DoctorSchedule::where('user_id', $validated['user_id'])
                ->where('schedule_date', $validated['schedule_date'])
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $doctorSchedule->id)
                ->where(function ($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
                })
                ->exists();

            if ($hasOverlap) {
                return back()->withInput()->withErrors([
                    'schedule_date' => 'แพทย์ท่านนี้มีตารางออกตรวจในช่วงเวลาดังกล่าวอยู่แล้ว',
                ]);
            }
        }

        $doctorSchedule->update($validated);

        return redirect()->route('doctor-schedules.index')
            ->with('success', 'อัปเดตตารางออกตรวจเรียบร้อยแล้ว');
    }

    public function destroy(DoctorSchedule $doctorSchedule): RedirectResponse
    {
        if ($doctorSchedule->queues()->exists()) {
            $doctorSchedule->update(['status' => 'cancelled']);

            return redirect()->route('doctor-schedules.index')
                ->with('success', 'ยกเลิกตารางตรวจเรียบร้อยแล้ว (เนื่องจากมีคิวถูกจองไว้แล้ว)');
        }

        $doctorSchedule->delete();

        return redirect()->route('doctor-schedules.index')
            ->with('success', 'ลบตารางออกตรวจเรียบร้อยแล้ว');
    }
}