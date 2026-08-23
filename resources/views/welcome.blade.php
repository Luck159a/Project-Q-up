<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'ระบบคิวอัจฉริยะ') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif

        <style>
            body { font-family: 'Instrument Sans', sans-serif; }
            /* สไตล์พื้นหลังโค้งๆ ลายน้ำจางๆ สไตล์การแพทย์ */
            .hero-bg {
                background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
                position: relative;
            }
            .hero-bg::before {
                content: '';
                position: absolute;
                top: 0; right: 0; bottom: 0; left: 0;
                background-image: radial-gradient(#dcfce7 2px, transparent 2px);
                background-size: 30px 30px;
                opacity: 0.3;
                z-index: 0;
            }
        </style>
    </head>
    <body class="bg-gray-50 font-sans antialiased text-gray-900">
    
    <div id="app">
        <div class="relative z-20">
            @include('layouts.navbar')
        </div>

        <main>
            <section class="hero-bg overflow-hidden pt-12 sm:pt-20 pb-20 sm:pb-32 lg:pb-36 z-10 relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                    <div class="lg:grid lg:grid-cols-12 lg:gap-16 items-center">
                        
                        <div class="sm:text-center md:max-w-2xl md:mx-auto lg:col-span-6 lg:text-left">
                            <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl lg:text-5xl xl:text-6xl leading-tight">
                                <span class="block">ระบบคิวโรงพยาบาล</span>
                                <span class="block text-green-600 mt-1">อัจฉริยะ SMART QUEUE</span>
                            </h1>
                            <p class="mt-4 text-lg text-gray-500 sm:mt-6 sm:text-xl lg:max-w-lg font-medium">
                                เปลี่ยนประสบการณ์รอคอย สู่บริการที่น่าประทับใจ ลดความวุ่นวายหน้าห้องตรวจให้เป็นเรื่องง่าย จัดการคิวอย่างชาญฉลาด
                            </p>
                            
                            <div class="mt-8 sm:max-w-lg sm:mx-auto sm:text-center lg:text-left lg:mx-0 flex flex-col sm:flex-row gap-4">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-bold rounded-full text-white bg-green-600 hover:bg-green-700 shadow-lg shadow-green-200 transition-all duration-300 transform hover:-translate-y-1">
                                        เข้าสู่แผงควบคุม (Dashboard)
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-bold rounded-full text-white bg-green-600 hover:bg-green-700 shadow-lg shadow-green-200 transition-all duration-300 transform hover:-translate-y-1">
                                        เข้าสู่ระบบ / จองคิว
                                    </a>
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 border-2 border-green-200 text-base font-bold rounded-full text-green-700 bg-white hover:bg-green-50 transition-all duration-300 transform hover:-translate-y-1">
                                        สมัครสมาชิกใหม่
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <div class="mt-16 relative sm:max-w-lg sm:mx-auto lg:mt-0 lg:max-w-none lg:mx-0 lg:col-span-6 lg:flex lg:items-center relative z-10">
                            <div class="absolute -top-10 -right-10 w-72 h-72 bg-green-100 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob"></div>
                            <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-teal-100 rounded-full mix-blend-multiply filter blur-2xl opacity-70 animate-blob animation-delay-2000"></div>
                            
                            <div class="relative mx-auto w-full max-w-md transform hover:scale-[1.02] transition duration-500">
                                <img class="w-full h-auto object-contain drop-shadow-2xl rounded-full" 
                                     src="{{ asset('images/hero-image.png') }}" 
                                     alt="Smart Hospital Queue Logo">
                            </div>
                            
                        </div>

                    </div>
                </div>
            </section>

            <section class="py-20 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <div class="text-center max-w-3xl mx-auto mb-16">
                        <h2 class="text-base font-bold tracking-wide text-green-600 uppercase">Updates & News</h2>
                        <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                            ข่าวสารประชาสัมพันธ์
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                        <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:shadow-xl hover:bg-white transition-all duration-300 transform hover:-translate-y-2 group cursor-pointer">
                            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <span class="text-2xl">🎉</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">ระบบใหม่เปิดให้บริการ</h3>
                            <p class="text-gray-500 leading-relaxed">
                                ตั้งแต่วันนี้เป็นต้นไป คุณสามารถเข้าใช้งานระบบจัดการคิวรูปแบบใหม่ได้แล้ว สะดวก รวดเร็ว และแม่นยำยิ่งขึ้น
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:shadow-xl hover:bg-white transition-all duration-300 transform hover:-translate-y-2 group cursor-pointer">
                            <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <span class="text-2xl">📢</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-red-600 transition-colors">ประกาศวันหยุดราชการ</h3>
                            <p class="text-gray-500 leading-relaxed">
                                ประกาศหยุดทำการในวันที่ 25 ธ.ค. เนื่องในวันหยุดราชการ สำหรับท่านที่มีนัดหมาย จะมีเจ้าหน้าที่ติดต่อกลับเพื่อเลื่อนคิว
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 hover:shadow-xl hover:bg-white transition-all duration-300 transform hover:-translate-y-2 group cursor-pointer">
                            <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                                <span class="text-2xl">📝</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors">เปิดรับสมัครสมาชิกใหม่</h3>
                            <p class="text-gray-500 leading-relaxed">
                                สมัครสมาชิกวันนี้ พร้อมสิทธิพิเศษในการจองคิวล่วงหน้า เช็คประวัติการรักษา และบริการแจ้งเตือนผ่านระบบ
                            </p>
                        </div>

                    </div>
                </div>
            </section>

        </main>
        
        <footer class="bg-gray-900 py-8 text-center text-gray-400 text-sm">
            <p>&copy; {{ date('Y') }} ระบบคิวโรงพยาบาลอัจฉริยะ (Smart Queue). All rights reserved.</p>
        </footer>
    </div>
    
    </body>
</html>