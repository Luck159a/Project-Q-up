@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-center">ตารางการจองคิว</h1>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-cyan-400">
                    <th class="border border-gray-300 px-4 py-3 font-bold text-left">เวลา</th>
                    <th class="border border-gray-300 px-4 py-3 font-bold">จันทร์</th>
                    <th class="border border-gray-300 px-4 py-3 font-bold">อังคาร</th>
                    <th class="border border-gray-300 px-4 py-3 font-bold">พุธ</th>
                    <th class="border border-gray-300 px-4 py-3 font-bold">พฤหัสบดี</th>
                    <th class="border border-gray-300 px-4 py-3 font-bold">ศุกร์</th>
                    <th class="border border-gray-300 px-4 py-3 font-bold">เสาร์</th>
                    <th class="border border-gray-300 px-4 py-3 font-bold">อาทิตย์</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $times = [];
                    $startHour = 9;
                    $startMinute = 0;
                    $endHour = 12;
                    
                    // Generate time slots every 20 minutes from 09:00 to 12:00
                    $currentHour = $startHour;
                    $currentMinute = $startMinute;
                    
                    while ($currentHour < $endHour || ($currentHour == $endHour && $currentMinute <= 0)) {
                        $times[] = sprintf('%02d:%02d', $currentHour, $currentMinute);
                        
                        $currentMinute += 20;
                        if ($currentMinute >= 60) {
                            $currentMinute -= 60;
                            $currentHour += 1;
                        }
                        
                        if ($currentHour > $endHour) {
                            break;
                        }
                    }
                    
                    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                @endphp

                @foreach($times as $time)
                    <tr>
                        <td class="border border-gray-300 px-4 py-3 font-bold bg-gray-100">{{ $time }}</td>
                        @foreach($days as $day)
                            <td class="border border-gray-300 px-4 py-3 h-20 hover:bg-blue-100 cursor-pointer transition" 
                                onclick="bookQueue('{{ $day }}', '{{ $time }}')">
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8 text-center">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            ดูการจองของฉัน
        </button>
    </div>
</div>

<script>
    function bookQueue(day, time) {
        alert(`คุณเลือก ${day} เวลา ${time}`);
        // ที่นี่สามารถเรียก API เพื่อจองคิว
    }
</script>

@endsection
