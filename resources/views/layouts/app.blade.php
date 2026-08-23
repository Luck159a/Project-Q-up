<!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- SweetAlert2: ใช้แสดง Toast แจ้งเตือน (สำเร็จ/ผิดพลาด) และ Confirmation Dialog ทั่วทั้งระบบ -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.12.0/sweetalert2.all.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Toast อัตโนมัติจาก session flash (success / errors) หลังทำรายการสำเร็จหรือล้มเหลว --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if (session('success'))
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: @json(session('success')),
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                @endif

                @if ($errors->any())
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: @json($errors->first()),
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                    });
                @endif
            });

            /**
             * ใช้แทน onsubmit="return confirm(...)" แบบเดิม — เรียกจาก <form onsubmit="return confirmSubmit(event, {...})">
             * ตัวอย่าง: <form onsubmit="return confirmSubmit(event, { title: 'ยกเลิกคิว?', confirmButtonText: 'ยืนยันยกเลิก' })">
             */
            function confirmSubmit(event, options = {}) {
                event.preventDefault();
                const form = event.target;

                Swal.fire({
                    title: options.title || 'ยืนยันการทำรายการ?',
                    text: options.text || 'การกระทำนี้ไม่สามารถย้อนกลับได้',
                    icon: options.icon || 'warning',
                    showCancelButton: true,
                    confirmButtonText: options.confirmButtonText || 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: options.confirmButtonColor || '#dc2626',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

                return false;
            }
        </script>
    </body>
</html>