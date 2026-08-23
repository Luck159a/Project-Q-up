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

    public function index(Request $request)
    {
        $user = Auth::user();

        // 🌟 ปรับปรุง: เฉพาะ Patient เท่านั้นที่จะถูก Redirect ไปดูประวัติตนเอง (Staff/Doctor/Admin จะเข้าดูหน้านี้ได้)
        if ($user->isPatient()) {
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

    public function book()
    {
        // 🌟 ปรับปรุง: อนุญาตให้ทุก Role (รวมถึง Doctor/Staff) เข้าหน้าจองคิวได้
        $schedules = DoctorSchedule::with('user')
            ->where('schedule_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('schedule_date')
            ->get();

        return view('queues.book', compact('schedules'));
    }

    public function doctorToday()
    {
        $user = Auth::user();
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

    public function create(int $scheduleId)
    {
        $schedule = DoctorSchedule::with(['user', 'queues'])->findOrFail($scheduleId);
        $slots = $this->queueService->buildSlots($schedule);

        return view('queues.create', compact('schedule', 'slots'));
    }

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

    public function success(int $id)
    {
        $queue = Queue::with(['user', 'doctorSchedule.user'])->findOrFail($id);
        [$queueBeforeCount, $myOrder] = $this->calculateQueuePosition($queue);

        return view('queues.success', compact('queue', 'queueBeforeCount', 'myOrder'));
    }

    public function history()
    {
        $queues = Queue::with(['doctorSchedule.user'])
            ->where('userId', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('queues.history', compact('queues'));
    }

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

        if ($queue->status === Queue::STATUS_IN_SERVICE) {
            broadcast(new QueueCalled($queue))->toOthers();
        }

        return back()->with('success', 'อัปเดตสถานะสำเร็จ!');
    }

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

        return redirect()->back()->with('success', 'ยกเลิกคิวเรียบร้อยแล้ว');
    }

    public function exportTicketPDF(int $id)
    {
        $queue = Queue::with(['user', 'doctorSchedule.user'])->findOrFail($id);

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

    public function exportPDF(Request $request)
    {
        // 🌟 ปรับปรุง: อนุญาตให้ Staff, Doctor และ Admin ออกรายงาน PDF ได้
        $user = Auth::user();
        if ($user->isPatient()) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงรายงานนี้');
        }

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
        } else {
            $today = Carbon::today()->toDateString();
            $query->whereHas('doctorSchedule', function ($q) use ($today) {
                $q->where('schedule_date', $today);
            });
        }

        $queues = $query->orderBy('labelNo')->limit(500)->get();

        $pdf = Pdf::loadView('reports.all_queues_pdf', compact('queues'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isRemoteEnabled' => true,
                'defaultFont' => 'Sarabun',
            ]);

        return $pdf->stream('Queue-Report.pdf');
    }

    protected function calculateQueuePosition(Queue $queue): array
    {
        $queueBeforeCount = Queue::where('docschId', $queue->docschId)
            ->where('id', '<', $queue->id)
            ->whereIn('status', Queue::ACTIVE_STATUSES)
            ->count();

        return [$queueBeforeCount, $queueBeforeCount + 1];
    }
}