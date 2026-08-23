<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('แผงควบคุม (Dashboard)') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-10 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Banner -->
            <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 rounded-3xl shadow-lg text-white p-6 sm:p-8">
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-center md:text-left">
                        <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">
                            ยินดีต้อนรับกลับ, {{ Auth::user()->name }}! 👋
                        </h3>
                        <p class="text-indigo-100 text-sm sm:text-base font-normal">
                            พร้อมที่จะจองคิวเพื่อเข้ารับบริการแล้วหรือยัง? เริ่มต้นได้ง่ายๆ ที่นี่
                        </p>
                    </div>
                    <a href="{{ route('doctor-schedules.index') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3.5 bg-white text-indigo-600 font-bold text-sm sm:text-base rounded-2xl hover:bg-indigo-50 transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 whitespace-nowrap shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        จองคิวแพทย์ทันที
                    </a>
                </div>
                <!-- Layer ตกแต่งลายนวลหลัง Banner -->
                <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- ฝั่งซ้าย: แนะนำวิธีการใช้งานระบบ (How It Works) -->
                <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-8">
                    <h4 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                        <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        แนะนำวิธีการใช้งานระบบ (How It Works)
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Step 1 -->
                        <div class="p-6 bg-indigo-50/60 rounded-2xl border border-indigo-100/50 flex flex-col justify-start text-center">
                            <div class="w-14 h-14 bg-indigo-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h5 class="font-bold text-base text-slate-800 mb-2">1. เลือกแพทย์และวันที่</h5>
                            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">เข้าไปที่หน้า "จองคิว" เลือกแพทย์ที่ต้องการและวันที่สะดวกเข้ารับบริการ</p>
                        </div>
                        
                        <!-- Step 2 -->
                        <div class="p-6 bg-purple-50/60 rounded-2xl border border-purple-100/50 flex flex-col justify-start text-center">
                            <div class="w-14 h-14 bg-purple-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h5 class="font-bold text-base text-slate-800 mb-2">2. จองช่วงเวลา</h5>
                            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">เลือกช่วงเวลาที่ยัง "ว่าง" ระบุอาการเบื้องต้น แล้วกดยืนยันการจอง</p>
                        </div>
                        
                        <!-- Step 3 -->
                        <div class="p-6 bg-emerald-50/60 rounded-2xl border border-emerald-100/50 flex flex-col justify-start text-center">
                            <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <h5 class="font-bold text-base text-slate-800 mb-2">3. รับบัตรคิวและรอเรียก</h5>
                            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">ระบบจะออกบัตรคิวให้ สามารถตรวจสอบสถานะได้ที่หน้า "ประวัติการจอง"</p>
                        </div>
                    </div>
                </div>

                <!-- ฝั่งขวา: เมนูใช้งานบ่อย (Quick Actions) -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 sm:p-8">
                    <h4 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-3">
                        <div class="p-2.5 bg-amber-50 text-amber-500 rounded-2xl">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        เมนูใช้งานบ่อย
                    </h4>
                    
                    <div class="space-y-3">
                        <!-- Quick Action 1 -->
                        <a href="{{ route('doctor-schedules.index') }}" 
                           class="flex items-center justify-between p-4 bg-slate-50 hover:bg-indigo-50/60 rounded-2xl border border-slate-100 transition duration-200 group">
                            <div class="flex items-center gap-3.5">
                                <div class="p-2.5 bg-white text-indigo-600 rounded-xl shadow-sm group-hover:scale-105 transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-slate-700 text-sm sm:text-base group-hover:text-indigo-600 transition">เริ่มจองคิวใหม่</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <!-- Quick Action 2 -->
                        <a href="{{ route('queue.history') }}" 
                           class="flex items-center justify-between p-4 bg-slate-50 hover:bg-purple-50/60 rounded-2xl border border-slate-100 transition duration-200 group">
                            <div class="flex items-center gap-3.5">
                                <div class="p-2.5 bg-white text-purple-600 rounded-xl shadow-sm group-hover:scale-105 transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-slate-700 text-sm sm:text-base group-hover:text-purple-600 transition">ประวัติการจองของฉัน</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>

                        <!-- Quick Action 3 -->
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl border border-slate-100 transition duration-200 group">
                            <div class="flex items-center gap-3.5">
                                <div class="p-2.5 bg-white text-slate-600 rounded-xl shadow-sm group-hover:scale-105 transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="font-semibold text-slate-700 text-sm sm:text-base group-hover:text-slate-900 transition">แก้ไขข้อมูลส่วนตัว</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</x-app-layout>