<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-3">
            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            {{ __('ตารางตรวจของคุณวันนี้') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-8 flex flex-wrap gap-2 p-1.5 bg-white border border-gray-100 rounded-2xl shadow-sm w-full md:w-fit">
                
                <a href="#" class="flex items-center px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl shadow-sm transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    ตารางงานของตัวเองในวันนี้
                </a>

                <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-2.5 text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 font-medium rounded-xl transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    หน้าหลัก (Dashboard)
                </a>

            </div>

            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-3xl shadow-lg p-8 text-white mb-8 flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
                <div class="absolute -right-10 -top-10 opacity-10">
                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path></svg>
                </div>
                
                <div class="relative z-10 mb-4 md:mb-0">
                    <p class="text-indigo-100 font-semibold tracking-wider text-sm mb-1 uppercase">ประจำวันที่</p>
                    <h3 class="text-3xl font-bold">{{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}</h3>
                </div>
                
                <div class="relative z-10 bg-white/20 backdrop-blur-sm border border-white/30 p-4 rounded-2xl text-center min-w-[150px]">
                    <p class="text-indigo-100 text-sm font-semibold mb-1">จำนวนคิวทั้งหมด</p>
                    <p class="text-4xl font-black">{{ $totalQueuesToday }} <span class="text-lg font-normal">คน</span></p>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h4 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center">
                    <span class="bg-indigo-500 w-1.5 h-6 mr-3 rounded-full"></span>
                    รายชื่อคนไข้ตามช่วงเวลา
                </h4>

                @if($totalQueuesToday > 0)
                    <div class="space-y-4">
                        @foreach($todayQueues as $queue)
                            <div class="group flex items-center justify-between p-5 rounded-2xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-100 transition-all duration-300">
                                <div class="flex items-center gap-6">
                                    <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100 text-indigo-700 font-black text-lg min-w-[120px] text-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        {{ $queue->period }} น.
                                    </div>
                                    
                                    <div>
                                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">เลขคิว: <span class="text-indigo-500">{{ $queue->labelNo }}</span></p>
                                        <p class="text-lg font-bold text-gray-900">{{ $queue->user->name ?? 'ไม่ระบุชื่อ' }}</p>
                                        @if($queue->Note)
                                            <p class="text-sm text-gray-600 mt-1 flex items-start gap-1">
                                                <svg class="w-4 h-4 mt-0.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                {{ $queue->Note }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <span class="px-4 py-2 rounded-full text-xs font-bold shadow-sm
                                        {{ $queue->status === 'รอเรียก' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : '' }}
                                        {{ $queue->status === 'กำลังใช้บริการ' ? 'bg-blue-100 text-blue-700 border border-blue-200' : '' }}
                                        {{ $queue->status === 'เสร็จสิ้น' ? 'bg-green-100 text-green-700 border border-green-200' : '' }}">
                                        {{ $queue->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-500 mb-4">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <h5 class="text-lg font-bold text-gray-900 mb-2">วันนี้ยังไม่มีคิวตรวจครับ</h5>
                        <p class="text-gray-500 text-sm">คุณสามารถพักผ่อนหรือเตรียมตัวสำหรับวันถัดไปได้เลย</p>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>