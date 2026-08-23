<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('เลือกแพทย์ที่ต้องการจองคิว') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @forelse($schedules as $schedule)
                    <div class="flex items-center justify-between p-5 mb-4 border border-gray-100 rounded-2xl hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="text-lg font-bold text-gray-900">{{ $schedule->user->name ?? 'ไม่ระบุแพทย์' }}</p>
                            <p class="text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('d F Y') }}
                                &middot;
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                -
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            </p>
                        </div>

                        <a href="{{ route('queues.create', $schedule->id) }}"
                           class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-lg font-semibold text-sm hover:bg-blue-700 transition shadow-sm">
                            เลือกช่วงเวลา
                        </a>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-500">
                        ยังไม่มีตารางแพทย์ที่เปิดให้จองในขณะนี้
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>