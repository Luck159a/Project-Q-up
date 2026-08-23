<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ระบบวิเคราะห์ข้อมูลผู้ใช้งาน') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-10">
                <h3 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center">
                    <span class="bg-indigo-600 w-2 h-7 mr-3 rounded-full"></span>
                    📊 สถิติผู้ใช้งานและคิววันนี้ (คลิกที่การ์ดเพื่อดูกราฟย้อนหลัง)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    
                    <div class="bg-indigo-600 rounded-3xl p-6 shadow-xl shadow-indigo-200 text-white transform hover:scale-105 transition flex flex-col justify-between">
                        <div>
                            <p class="text-indigo-200 font-bold uppercase tracking-widest text-xs">คิวทั้งหมดวันนี้</p>
                            <p class="text-6xl font-black mt-4">
                                {{ $stats['daily']['queues'] ?? 0 }} 
                                <span class="text-2xl text-indigo-300 font-bold">คิว</span>
                            </p>
                        </div>
                        <div class="mt-4 bg-white/20 p-2 rounded-lg text-xs font-semibold tracking-wide text-center backdrop-blur-sm">
                            🟢 อัปเดตข้อมูลเรียลไทม์
                        </div>
                    </div>

                    <div onclick="toggleChart('doctor', 'แพทย์ (ใหม่)', 'rgb(249, 115, 22)', 'rgba(249, 115, 22, 0.2)')" 
                         class="cursor-pointer bg-white border-2 border-orange-500 rounded-3xl p-6 shadow-md transform transition hover:scale-105 hover:shadow-orange-200 flex flex-col justify-between">
                        <div>
                            <p class="text-orange-600 font-black uppercase tracking-widest text-xs">แพทย์ (ใหม่)</p>
                            <p class="text-gray-900 text-5xl font-black mt-4">
                                {{ $stats['daily']['doctor'] ?? 0 }} 
                                <span class="text-xl text-gray-400 font-bold">คน</span>
                            </p>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-4 font-semibold bg-gray-50 p-2 rounded-lg border border-gray-100">
                            มีคิวที่เกี่ยวข้อง: <span class="text-orange-600 font-bold">{{ $stats['daily']['queues_doctor'] ?? 0 }}</span> คิว
                        </p>
                    </div>

                    <div onclick="toggleChart('staff', 'เจ้าหน้าที่ (ใหม่)', 'rgb(59, 130, 246)', 'rgba(59, 130, 246, 0.2)')"
                         class="cursor-pointer bg-white border-2 border-blue-500 rounded-3xl p-6 shadow-md transform transition hover:scale-105 hover:shadow-blue-200 flex flex-col justify-between">
                        <div>
                            <p class="text-blue-600 font-black uppercase tracking-widest text-xs">เจ้าหน้าที่ (ใหม่)</p>
                            <p class="text-gray-900 text-5xl font-black mt-4">
                                {{ $stats['daily']['staff'] ?? 0 }} 
                                <span class="text-xl text-gray-400 font-bold">คน</span>
                            </p>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-4 font-semibold bg-gray-50 p-2 rounded-lg border border-gray-100">
                            มีคิวที่เกี่ยวข้อง: <span class="text-blue-600 font-bold">{{ $stats['daily']['queues_staff'] ?? 0 }}</span> คิว
                        </p>
                    </div>

                    <div onclick="toggleChart('patient', 'คนไข้ (ใหม่)', 'rgb(34, 197, 94)', 'rgba(34, 197, 94, 0.2)')"
                         class="cursor-pointer bg-white border-2 border-green-500 rounded-3xl p-6 shadow-md transform transition hover:scale-105 hover:shadow-green-200 flex flex-col justify-between">
                        <div>
                            <p class="text-green-600 font-black uppercase tracking-widest text-xs">คนไข้ (ใหม่)</p>
                            <p class="text-gray-900 text-5xl font-black mt-4">
                                {{ $stats['daily']['patient'] ?? 0 }} 
                                <span class="text-xl text-gray-400 font-bold">คน</span>
                            </p>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-4 font-semibold bg-gray-50 p-2 rounded-lg border border-gray-100">
                            มีคิวที่เกี่ยวข้อง: <span class="text-green-600 font-bold">{{ $stats['daily']['queues_patient'] ?? 0 }}</span> คิว
                        </p>
                    </div>

                </div>

                <div id="chartContainer" class="hidden mt-8 bg-white p-6 rounded-3xl shadow-sm border border-gray-100 transition-all duration-500 ease-in-out transform opacity-0 scale-95">
                    <div class="flex justify-between items-center mb-4">
                        <h4 id="chartTitle" class="text-lg font-bold text-gray-800">สถิติย้อนหลัง 7 วัน</h4>
                        <button onclick="closeChart()" class="text-gray-400 hover:text-red-500 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>

            </div>

            <hr class="my-12 border-gray-200">

            <div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center">
                    <span class="bg-indigo-600 w-2 h-7 mr-3 rounded-full"></span>
                    📅 สรุปยอดสะสมเดือนนี้
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 border-b-8 border-b-orange-500">
                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">Doctors ทั้งหมด</p>
                        <p class="text-5xl font-black text-gray-900 mt-3">{{ $stats['monthly']['doctor'] ?? 0 }} <span class="text-xl font-bold text-gray-300">ราย</span></p>
                    </div>
                    
                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 border-b-8 border-b-blue-500">
                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">Staff ทั้งหมด</p>
                        <p class="text-5xl font-black text-gray-900 mt-3">{{ $stats['monthly']['staff'] ?? 0 }} <span class="text-xl font-bold text-gray-300">ราย</span></p>
                    </div>

                    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 border-b-8 border-b-green-500">
                        <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">Patient ทั้งหมด</p>
                        <p class="text-5xl font-black text-gray-900 mt-3">{{ $stats['monthly']['patient'] ?? 0 }} <span class="text-xl font-bold text-gray-300">ราย</span></p>
                    </div>
                </div>
            </div>

            <div class="mt-12 bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-black text-gray-900">📊 ยอดรวมทั้งหมดในระบบ</h3>
                    <p class="text-gray-500 mt-1">สรุปภาพรวมผู้ใช้งานและคิวสะสมตั้งแต่เปิดระบบ</p>
                </div>
                <div class="flex gap-8 md:gap-12 w-full md:w-auto justify-around">
                    <div class="text-center">
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1">คนไข้สะสม</p>
                        <p class="text-4xl font-black text-emerald-600">{{ $stats['total']['patient'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-1">คิวสะสมทั้งหมด</p>
                        <p class="text-4xl font-black text-indigo-600">{{ $stats['total']['queues'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        let myChartInstance = null;
        let currentType = '';

        // ข้อมูลจำลอง (Mock Data) สำหรับกราฟย้อนหลัง 6 วัน + วันนี้ (ตัวจริง)
        const chartData = {
            'doctor': [1, 0, 2, 1, 0, 1, {{ $stats['daily']['doctor'] ?? 0 }}],
            'staff':  [0, 1, 0, 0, 2, 0, {{ $stats['daily']['staff'] ?? 0 }}],
            'patient':[12, 15, 8, 20, 14, 18, {{ $stats['daily']['patient'] ?? 0 }}]
        };

        const labels = ['6 วันก่อน', '5 วันก่อน', '4 วันก่อน', '3 วันก่อน', 'เมื่อวานซืน', 'เมื่อวาน', 'วันนี้'];

        function toggleChart(type, titleName, borderColor, backgroundColor) {
            const container = document.getElementById('chartContainer');
            
            if (currentType === type && !container.classList.contains('hidden')) {
                closeChart();
                return;
            }

            currentType = type;
            document.getElementById('chartTitle').innerText = `สถิติการสมัคร ${titleName} ย้อนหลัง 7 วัน`;

            container.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('opacity-0', 'scale-95');
                container.classList.add('opacity-100', 'scale-100');
            }, 10);

            renderChart(type, borderColor, backgroundColor);
        }

        function closeChart() {
            const container = document.getElementById('chartContainer');
            container.classList.remove('opacity-100', 'scale-100');
            container.classList.add('opacity-0', 'scale-95');
            
            setTimeout(() => {
                container.classList.add('hidden');
                currentType = '';
            }, 500);
        }

        function renderChart(type, borderColor, backgroundColor) {
            const ctx = document.getElementById('myChart').getContext('2d');

            if (myChartInstance) {
                myChartInstance.destroy();
            }

            myChartInstance = new Chart(ctx, {
                type: 'line', 
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'จำนวนคน',
                        data: chartData[type],
                        borderColor: borderColor,
                        backgroundColor: backgroundColor,
                        borderWidth: 3,
                        pointBackgroundColor: borderColor,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false } 
                    },
                    scales: {
                        y: {
                            beginAtZero: true,  
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }
    </script>
</x-app-layout>