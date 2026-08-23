<?php

namespace App\Http\Controllers;

use App\Events\QueueCalled;
use App\Http\Requests\StoreQueueRequest;
use App\Http\Requests\UpdateQueueStatusRequest;
use App\Models\DoctorSchedule;
use App\Models\Queue;
use App\Services\QueueService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class QueueController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    /**
     * Display: หน้ารวมรายการคิว (Staff/Admin เท่านั้น — บังคับด้วย route middleware 'role:admin,staff')
     */
    public function index(Request $request)
    {
        if (Auth::user()->isPatient()) {
            return redirect()->route('queue.history');
        }

        $date = $request->input('date');
        $search = $request->input('search');

        $query = Queue::with(['user', 'doctorSchedule.user']);

        if ($date === 'today') {
            $today = Carbon::today()->toDateString();
            $query->whereHas('doctorSchedule', function ($q) use ($today) {
                $q->where('schedule_date', $today);
            });
        } elseif ($date) {
            $query->whereHas('doctorSchedule', function ($q) use ($date) {
                $q->where('schedule_date', $date);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('labelNo', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('doctorSchedule.user', function ($d) use ($search) {
                        $d->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $queues = $query->orderBy('labelNo')
            ->paginate(10)
            ->appends($request->query());

        $availableDates = DoctorSchedule::select('schedule_date')
            ->distinct()
            ->orderBy('schedule_date')
            ->get();

        return view('queues.index', compact('queues', 'availableDates'));
    }

    /**
     * Book: หน้าเลือกตารางหมอ (หมอจะเห็นคิวของตัวเองวันนี้แทน)
     */
    public function book()
    {
        $user = Auth::user();

        if ($user->isDoctor()) {
            $today = Carbon::today()->toDateString();

            $todayQueues = Queue::with(['user', 'doctorSchedule'])
                ->whereHas('doctorSchedule', function ($q) use ($user, $today) {
                    $q->where('user_id', $user->id)
                        ->where('schedule_date', $today);
                })
                ->where('status', '!=', Queue::STATUS_CANCELLED)
                ->orderBy('period')
                ->get();

            $totalQueuesToday = $todayQueues->count();

            return view('queues.doctor_today', compact('todayQueues', 'totalQueuesToday'));
        }

        $schedules = DoctorSchedule::with('user')
            ->where('schedule_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('schedule_date')
            ->get();

        return view('queues.book', compact('schedules'));
    }

    /**
     * Create View: เลือกช่วงเวลาที่จะจอง
     */
    public function create(int $scheduleId)
    {
        $schedule = DoctorSchedule::with(['user', 'queues'])->findOrFail($scheduleId);
        $slots = $this->queueService->buildSlots($schedule);

        return view('queues.create', compact('schedule', 'slots'));
    }

    /**
     * Store: บันทึกข้อมูลการจองคิว (ตรรกะทั้งหมดอยู่ใน QueueService::bookQueue
     * ซึ่งใช้ DB Transaction + lockForUpdate ป้องกัน Race Condition)
     */
    public function store(StoreQueueRequest $request): RedirectResponse
    {
        try {
            $queue = $this->queueService->bookQueue(
                scheduleId: (int) $request->validated('docschId'),
                period: $request->validated('period'),
                note: $request->validated('Note'),
                patient: $request->user(),
                actor: $request->user(),
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('queue.success', $queue->id)->with('success', 'จองคิวสำเร็จแล้ว!');
    }

    /**
     * Success: หน้าแสดงใบยืนยันหลังจองสำเร็จ
     */
    public function success(int $id)
    {
        $queue = Queue::with(['user', 'doctorSchedule.user'])->findOrFail($id);
        [$queueBeforeCount, $myOrder] = $this->calculateQueuePosition($queue);

        return view('queues.success', compact('queue', 'queueBeforeCount', 'myOrder'));
    }

    /**
     * History: ดูประวัติการจอง (สำหรับคนไข้)
     */
    public function history()
    {
        $queues = Queue::with(['doctorSchedule.user'])
            ->where('userId', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('queues.history', compact('queues'));
    }

    /**
     * Update Status: สำหรับเจ้าหน้าที่เรียกคิว หรือจบงาน (ตรวจ State Machine + สิทธิ์ใน Service)
     */
    public function updateStatus(UpdateQueueStatusRequest $request, int $id): RedirectResponse
    {
        try {
            $queue = $this->queueService->changeStatus(
                queueId: $id,
                newStatus: $request->validated('status'),
                actor: $request->user(),
                note: $request->validated('note'),
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }

        // แจ้งเตือนแบบ Real-time เมื่อเจ้าหน้าที่เรียกคิว (ดู app/Events/QueueCalled.php)
        if ($queue->status === Queue::STATUS_IN_SERVICE) {
            broadcast(new QueueCalled($queue))->toOthers();
        }

        return back()->with('success', 'อัปเดตสถานะสำเร็จ!');
    }

    /**
     * Cancel: ยกเลิกคิว (เจ้าของคิวเอง หรือ Staff/Admin — ตรวจสิทธิ์ใน Service)
     */
    public function cancel(Request $request, int $id): RedirectResponse
    {
        try {
            $this->queueService->changeStatus(
                queueId: $id,
                newStatus: Queue::STATUS_CANCELLED,
                actor: $request->user(),
            );
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }

        return redirect()->back()->with('success', 'ยกเลิกคิวเรียบร้อยแล้ว ช่วงเวลานี้จะกลับมาว่างอีกครั้ง');
    }

    /**
     * ฟังก์ชันสำหรับสร้างไฟล์ PDF ใบยืนยันคิว (รายคน)
     */
    public function exportTicketPDF(int $id)
    {
        $queue = Queue::with(['user', 'doctorSchedule.user'])->findOrFail($id);

        // เจ้าของคิวเอง หรือ Staff/Admin เท่านั้นที่ดาวน์โหลดใบคิวได้
        if ($queue->userId !== Auth::id() && ! Auth::user()->isStaffOrAdmin()) {
            abort(403);
        }

        [$queueBeforeCount, $myOrder] = $this->calculateQueuePosition($queue);

        $pdf = Pdf::loadView('reports.queue_ticket', compact('queue', 'queueBeforeCount', 'myOrder'))
            ->setPaper([0, 0, 400, 500], 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'Sarabun',
            ]);

        return $pdf->stream('Queue-Ticket-'.$queue->labelNo.'.pdf');
    }

    /**
     * ฟังก์ชันสำหรับส่งออกรายงานคิวทั้งหมด หรือตามเงื่อนไขการค้นหา (PDF) — Staff/Admin เท่านั้น
     */
    public function exportPDF(Request $request)
    {
        $query = Queue::with(['user', 'doctorSchedule.user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('labelNo', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('doctorSchedule.user', function ($d) use ($search) {
                        $d->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date')) {
            $query->whereHas('doctorSchedule', function ($q) use ($request) {
                $q->where('schedule_date', $request->date);
            });
        }

        $queues = $query->orderBy('labelNo')->get();

        $pdf = Pdf::loadView('reports.all_queues_pdf', compact('queues'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'Sarabun',
            ]);

        return $pdf->stream('Queue-Report.pdf');
    }

    /**
     * @return array{0: int, 1: int} [จำนวนคิวก่อนหน้า, ลำดับของฉัน]
     */
    protected function calculateQueuePosition(Queue $queue): array
    {
        $queueBeforeCount = Queue::where('docschId', $queue->docschId)
            ->where('id', '<', $queue->id)
            ->whereIn('status', Queue::ACTIVE_STATUSES)
            ->count();

        return [$queueBeforeCount, $queueBeforeCount + 1];
    }
}