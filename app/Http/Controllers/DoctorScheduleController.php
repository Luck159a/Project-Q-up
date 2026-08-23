<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorScheduleController extends Controller
{
    /**
     * สิทธิ์การเข้าถึงหลักถูกบังคับที่ routes/web.php แล้ว (middleware 'role:admin,staff'
     * ครอบทุก action ยกเว้น index/show) การเช็ก Auth::user()->hasRole() ในแต่ละ method
     * ด้านล่างเป็นการป้องกันซ้ำอีกชั้น (defense in depth) เผื่อมีคนเรียก method ตรงๆ
     * โดยไม่ผ่าน route (เช่นเทส หรือโค้ดส่วนอื่นในอนาคต)
     */

    public function index(Request $request)
    {
        $query = DoctorSchedule::with('user');

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $schedules = $query->orderBy('schedule_date', 'desc')->paginate(10);
        return view('doctor_schedules.index', compact('schedules'));
    }

    public function create()
    {
        if (! Auth::check() || ! Auth::user()->isStaffOrAdmin()) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        $doctors = User::where('role', 'doctor')->get();
        return view('doctor_schedules.form', compact('doctors'));
    }

    public function store(Request $request)
    {
        if (! Auth::check() || ! Auth::user()->isStaffOrAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'nullable|string'
        ]);

        DoctorSchedule::create($validated);
        return redirect()->route('doctor-schedules.index')->with('success', 'บันทึกตารางเวลาสำเร็จ');
    }

    public function show(DoctorSchedule $doctorSchedule)
    {
        return view('doctor_schedules.show', compact('doctorSchedule'));
    }

    public function edit(DoctorSchedule $doctorSchedule)
    {
        if (! Auth::check() || ! Auth::user()->isStaffOrAdmin()) {
            abort(403);
        }

        $doctors = User::where('role', 'doctor')->get();
        return view('doctor_schedules.form', [
            'schedule' => $doctorSchedule,
            'doctors' => $doctors
        ]);
    }

    public function update(Request $request, DoctorSchedule $doctorSchedule)
    {
        if (! Auth::check() || ! Auth::user()->isStaffOrAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'status' => 'required|string'
        ]);

        $doctorSchedule->update($validated);
        return redirect()->route('doctor-schedules.index')->with('success', 'อัปเดตตารางเวลาสำเร็จ');
    }

    public function destroy(DoctorSchedule $doctorSchedule)
    {
        if (! Auth::check() || ! Auth::user()->isStaffOrAdmin()) {
            abort(403);
        }

        $doctorSchedule->delete();
        return redirect()->route('doctor-schedules.index')->with('success', 'ลบตารางเวลาเรียบร้อยแล้ว');
    }
}