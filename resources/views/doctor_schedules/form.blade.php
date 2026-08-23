<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($schedule) ? 'แก้ไขตารางเวลา' : 'เพิ่มตารางเวลาใหม่' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <form method="POST" action="{{ isset($schedule) ? route('doctor-schedules.update', $schedule->id) : route('doctor-schedules.store') }}">
                    @csrf
                    @if(isset($schedule))
                        @method('PUT')
                    @endif

                    <div class="mb-6">
                        <x-input-label for="user_id" :value="__('เลือกคุณหมอ')" />
                        <select name="user_id" id="user_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">-- กรุณาเลือกหมอ --</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ (old('user_id', $schedule->user_id ?? '') == $doctor->id) ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="schedule_date" :value="__('วันที่ตรวจ')" />
                        <x-text-input id="schedule_date" class="block mt-1 w-full" type="date" name="schedule_date" 
                                      value="{{ old('schedule_date', $schedule->schedule_date ?? '') }}" required />
                        <x-input-error :messages="$errors->get('schedule_date')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="start_time" :value="__('เวลาเริ่ม')" />
                            <x-text-input id="start_time" class="block mt-1 w-full" type="time" name="start_time" 
                                          value="{{ old('start_time', $schedule->start_time ?? '') }}" required />
                        </div>

                        <div>
                            <x-input-label for="end_time" :value="__('เวลาสิ้นสุด')" />
                            <x-text-input id="end_time" class="block mt-1 w-full" type="time" name="end_time" 
                                          value="{{ old('end_time', $schedule->end_time ?? '') }}" required />
                        </div>
                    </div>

                    <div class="mb-8">
                        <x-input-label for="status" :value="__('สถานะ')" />
                        <select name="status" id="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="available" {{ (old('status', $schedule->status ?? '') == 'available') ? 'selected' : '' }}>ว่าง (Available)</option>
                            <option value="booked" {{ (old('status', $schedule->status ?? '') == 'booked') ? 'selected' : '' }}>จองแล้ว (Booked)</option>
                            <option value="cancelled" {{ (old('status', $schedule->status ?? '') == 'cancelled') ? 'selected' : '' }}>ยกเลิก (Cancelled)</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('doctor-schedules.index') }}" class="text-sm text-gray-600 hover:underline">ยกเลิก</a>
                        <x-primary-button>
                            {{ isset($schedule) ? 'บันทึกการแก้ไข' : 'สร้างตารางเวลา' }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>