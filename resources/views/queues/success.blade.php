<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('สถานะการจองคิว') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">จองคิวเข้ารับบริการสำเร็จ!</h3>
                    <p class="text-gray-500">หมายเลขคิวของคุณคือ</p>
                    <div class="mt-4 text-6xl font-black text-indigo-600 tracking-tighter">
                        {{ $queue->labelNo }}
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold">ชื่อแพทย์</p>
                            <p class="text-lg font-medium text-gray-800">{{ $queue->doctorSchedule->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold">ช่วงเวลาที่จอง</p>
                            <p class="text-lg font-medium text-gray-800">{{ $queue->period }} น.</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold">สถานะปัจจุบัน</p>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                {{ $queue->status }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 uppercase font-bold">วันที่บันทึก</p>
                            <p class="text-lg font-medium text-gray-800">{{ $queue->created_at->format('d/m/Y H:i') }} น.</p>
                        </div>
                    </div>

                    {{-- ✅ เพิ่มโค้ดลำดับคิวตรงนี้ เพื่อให้เป็นส่วนหนึ่งของรายละเอียดการจอง --}}
                    <div class="mt-6 p-4 bg-indigo-50 rounded-lg border border-indigo-100">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-indigo-600 font-medium">ลำดับคิวของคุณ</p>
                                <p class="text-2xl font-bold text-indigo-900">ที่ {{ $myOrder }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">มีคิวก่อนหน้าคุณ</p>
                                <p class="text-xl font-semibold text-orange-600">{{ $queueBeforeCount }} คิว</p>
                            </div>
                        </div>
                        
                        @if($queueBeforeCount > 0)
                            <p class="text-xs text-gray-400 mt-2">* กรุณาสแตนด์บายรอเรียกพบแพทย์</p>
                        @else
                            <p class="text-xs text-green-600 mt-2 font-medium">* ถึงคิวของคุณแล้ว โปรดเตรียมตัว</p>
                        @endif
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    @if(strtolower(auth()->user()->role) === 'patient')
                        <a href="{{ route('queue.history') }}" class="inline-flex justify-center items-center px-6 py-3 bg-white border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none">
                            ดูประวัติการจองของฉัน
                        </a>
                    @endif

                    @if(in_array(strtolower(auth()->user()->role), ['admin', 'staff']))
                        <a href="{{ route('queues.index') }}" class="inline-flex justify-center items-center px-6 py-3 bg-indigo-600 border border-transparent shadow-sm text-base font-medium rounded-md text-white hover:bg-indigo-700 focus:outline-none">
                            กลับไปหน้าจัดการคิวรวม
                        </a>
                    @endif

                    <a href="{{ url('/') }}" class="inline-flex justify-center items-center px-6 py-3 bg-gray-800 border border-transparent shadow-sm text-base font-medium rounded-md text-white hover:bg-gray-900 focus:outline-none">
                        กลับหน้าหลัก
                    </a>
                    {{-- เพิ่มต่อจากปุ่มกลับหน้าหลัก --}}
                    <a href="{{ route('queues.pdf', $queue->id) }}" class="inline-flex justify-center items-center px-6 py-3 bg-red-600 border border-transparent shadow-sm text-base font-medium rounded-md text-white hover:bg-red-700 focus:outline-none transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        ดาวน์โหลด PDF
                    </a>
                </div>

            </div>
        </div>
    </div> 
</x-app-layout>