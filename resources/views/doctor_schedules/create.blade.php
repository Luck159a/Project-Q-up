<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('เพิ่มตารางออกตรวจแพทย์') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('doctor-schedules.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- เลือกแพทย์ -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">แพทย์ผู้ตรวจ <span class="text-red-500">*</span></label>
                        <select name="user_id" id="user_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- กรุณาเลือกแพทย์ --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('user_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- วันที่ออกตรวจ -->
                    <div>
                        <label for="schedule_date" class="block text-sm font-medium text-gray-700 mb-1">วันที่ออกตรวจ <span class="text-red-500">*</span></label>
                        <input type="date" name="schedule_date" id="schedule_date" value="{{ old('schedule_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- ช่วงเวลา -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">เวลาเริ่มตรวจ <span class="text-red-500">*</span></label>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '09:00') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">เวลาสิ้นสุด <span class="text-red-500">*</span></label>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time', '12:00') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <!-- หมายเหตุ -->
                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุเพิ่มเติม</label>
                        <textarea name="note" id="note" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)">{{ old('note') }}</textarea>
                    </div>

                    <!-- ปุ่มดำเนินการ -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('doctor-schedules.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                            ยกเลิก
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            บันทึกตารางตรวจ
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>