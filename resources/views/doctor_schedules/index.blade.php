<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('ตารางเวลาแพทย์ & การจองคิว') }}
            </h2>
            
            {{-- ส่วนที่แก้ไข: เพิ่ม strtolower เพื่อให้ Staff (ตัวพิมพ์ใหญ่) มองเห็นปุ่มได้ --}}
            @if(Auth::check() && in_array(strtolower(Auth::user()->role), ['admin', 'staff']))
            <a href="{{ route('doctor-schedules.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                เพิ่มตารางเวลาใหม่
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form method="GET" action="{{ route('doctor-schedules.index') }}" class="mb-6 flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" placeholder="ค้นหาชื่อหมอ..." 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                               value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="inline-flex items-center px-5 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        ค้นหา
                    </button>
                </form>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">แพทย์</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">วันที่ให้บริการ</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">ช่วงเวลา</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($schedules as $item)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->user->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($item->schedule_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $item->start_time }} - {{ $item->end_time }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $item->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <span class="h-2 w-2 rounded-full mr-2 {{ $item->status == 'available' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $item->status == 'available' ? 'เปิดรับจอง' : 'งดให้บริการ' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center gap-4">
                                        
                                        @if($item->status == 'available')
                                            {{-- ส่วนที่แก้ไข: ให้ Staff เห็นปุ่มจัดการคิวเหมือน Admin --}}
                                            @if(Auth::check() && in_array(strtolower(Auth::user()->role), ['admin', 'staff']))
                                                <a href="{{ route('queues.index', ['schedule_id' => $item->id]) }}" 
                                                   class="bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700 transition shadow-sm text-xs font-bold">
                                                     จัดการคิว
                                                </a>
                                            @else
                                                <a href="{{ route('queues.create', $item->id) }}" 
                                                   class="bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700 transition shadow-sm text-xs">
                                                     จองคิว
                                                </a>
                                            @endif
                                        @endif

                                        {{-- ส่วนที่แก้ไข: ให้ Staff เห็นปุ่มแก้ไข/ลบ เหมือน Admin --}}
                                        @if(Auth::check() && in_array(strtolower(Auth::user()->role), ['admin', 'staff']))
                                        <div class="flex items-center border-l border-gray-300 pl-4 gap-4">
                                            <a href="{{ route('doctor-schedules.edit', $item) }}" class="text-amber-600 hover:text-amber-900 flex items-center gap-1 font-bold text-xs transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                แก้ไข
                                            </a>
                                            <form action="{{ route('doctor-schedules.destroy', $item) }}" method="POST" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 flex items-center gap-1 font-bold text-xs transition" onclick="return confirm('ยืนยันการลบตารางเวลานี้?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    ลบ
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-gray-500">ไม่พบข้อมูลตารางเวลาแพทย์ในขณะนี้</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $schedules->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>