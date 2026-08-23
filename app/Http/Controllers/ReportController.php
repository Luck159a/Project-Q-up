<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Queue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * สำหรับส่งออกรายงานผู้ใช้ทั้งหมด (Admin เท่านั้น)
     */
    public function exportUserPDF() 
    {
        if (!Auth::check() || strtolower(Auth::user()->role) !== 'admin') {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงรายงานนี้');
        }

        $users = User::orderBy('created_at', 'desc')->get();
        
        $pdf = Pdf::loadView('reports.users_pdf', compact('users'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions($this->getPdfOptions());

        return $pdf->stream('users_report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * สำหรับส่งออกรายงานคิวรายวัน (สำหรับ Staff/Admin)
     * รองรับทั้งการดูคิวของวันนี้ หรือเลือกระบุวันที่ต้องการได้
     */
    public function daily(Request $request)
    {
        if (!Auth::check() || !in_array(strtolower(Auth::user()->role), ['admin', 'staff'])) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงรายงานนี้');
        }

        $targetDate = $request->input('date', Carbon::today()->toDateString());

        // ดึงข้อมูลคิวอ้างอิงตามวันที่ออกตรวจจริงของแพทย์
        $queues = Queue::with(['user', 'doctorSchedule.user'])
                    ->whereHas('doctorSchedule', function ($query) use ($targetDate) {
                        $query->where('schedule_date', $targetDate);
                    })
                    ->orderBy('labelNo', 'asc')
                    ->get();

        $pdf = Pdf::loadView('reports.daily_queues', compact('queues', 'targetDate'))
                  ->setPaper('a4', 'landscape')
                  ->setOptions($this->getPdfOptions());
        
        return $pdf->stream('daily-report-' . $targetDate . '.pdf');
    }

    /**
     * ฟังก์ชันตัวช่วยสำหรับตั้งค่า PDF ให้รองรับภาษาไทยและฟอนต์ Sarabun
     */
    private function getPdfOptions()
    {
        return [
            'tempDir' => public_path(),
            'chroot'  => public_path(),
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Sarabun' 
        ];
    }
}