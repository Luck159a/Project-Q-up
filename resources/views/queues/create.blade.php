<x-app-layout>

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">

            {{ __('จองคิวเข้ารับบริการ: หมอ') }} {{ $schedule->user->name }}

        </h2>

    </x-slot>



    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">

               

                <div class="flex items-center justify-between mb-8 border-b pb-4">

                    <div>

                        <p class="text-sm text-gray-500 uppercase tracking-wider">วันที่รับบริการ</p>

                        <p class="text-xl font-bold text-indigo-900">{{ $schedule->schedule_date }}</p>

                    </div>

                    <div class="text-right">

                        <p class="text-sm text-gray-500 uppercase tracking-wider">สถานะ</p>

                        <p class="text-green-600 font-bold">เปิดรับการจอง</p>

                    </div>

                </div>



                <form action="{{ route('queues.store') }}" method="POST">

                    @csrf

                    <input type="hidden" name="docschId" value="{{ $schedule->id }}">

                    <input type="hidden" name="period" id="selected_period" required>



                    <h3 class="font-bold text-lg mb-6 flex items-center">

                        เลือกช่วงเวลาที่ต้องการ

                    </h3>



                    <div class="space-y-4 mb-8">

                        @foreach($slots as $slot)

                            <div class="flex items-center justify-between p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors">

                                <span class="text-lg font-semibold text-gray-700">{{ $slot['time'] }}</span>



                                <div class="relative inline-block">

                                    @if($slot['is_available'])

                                        <button type="button"

                                            onclick="selectTimeList(this, '{{ $slot['time'] }}')"

                                            class="time-button cursor-pointer py-2 px-6 rounded-md font-medium text-sm transition-all

                                                   bg-blue-500 text-white border border-blue-600 hover:bg-blue-600 shadow-sm">

                                            ว่าง

                                        </button>

                                    @else

                                        <div class="py-2 px-6 rounded-md font-medium text-sm

                                                    bg-red-500 text-white border border-red-600 opacity-60 cursor-not-allowed">

                                            เต็มแล้ว

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>



                    @error('period')

                        <p class="text-red-500 text-sm mt-2 mb-4">{{ $message }}</p>

                    @enderror



                    <div class="mb-8">

                        <label class="block font-bold text-gray-700 mb-2">หมายเหตุ (ระบุอาการเบื้องต้น)</label>

                        <textarea name="Note" rows="3"

                            placeholder="ระบุอาการเบื้องต้น..."

                            class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all"></textarea>

                    </div>



                    <div class="flex items-center justify-end border-t pt-6">

                        <button type="submit" class="bg-blue-600 text-white px-10 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all shadow-lg">

                            ยืนยันการจองคิว

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <script>
function selectTimeList(element, time) {
    const selectedInput = document.getElementById('selected_period');
    
    // 1. ตรวจสอบว่าปุ่มที่กด "ถูกเลือกอยู่แล้ว" หรือไม่ (เช็คจาก Class สีเขียว)
    if (element.classList.contains('bg-green-700')) {
        
        // --- กรณีกดซ้ำเพื่อ "ยกเลิก" ---
        element.classList.remove('bg-green-700', 'border-green-700');
        element.classList.add('bg-blue-500', 'border-blue-600');
        element.innerText = 'ว่าง';
        
        // ล้างค่าใน Hidden Input (ทำให้ค่าว่าง)
        selectedInput.value = '';
        
    } else {
        
        // --- กรณีกดเพื่อ "เลือก" ---
        
        // ล้างสีปุ่มอื่นๆ ทั้งหมดให้กลับเป็น "ว่าง" ก่อน
        const allButtons = document.querySelectorAll('.time-button');
        allButtons.forEach(btn => {
            btn.classList.remove('bg-green-700', 'border-green-700');
            btn.classList.add('bg-blue-500', 'border-blue-600');
            btn.innerText = 'ว่าง';
        });

        // เปลี่ยนปุ่มที่กดให้เป็นสีเขียว "เลือกแล้ว"
        element.classList.remove('bg-blue-500', 'border-blue-600');
        element.classList.add('bg-green-700', 'border-green-700');
        element.innerText = 'เลือกแล้ว';

        // ใส่ค่าเวลาลงใน Hidden Input
        selectedInput.value = time;
    }
}
</script>

</x-app-layout>