<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- โลโก้ -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/PNG Host.png') }}" alt="Logo" class="block h-12 w-auto object-contain">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        <!-- 1. เมนูหน้าแรก (Dashboard) -->
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('หน้าแรก') }}
                        </x-nav-link>

                        <!-- 🌟 เมนูสำหรับ DOCTOR -->
                        @if (strtolower(auth()->user()->role) === 'doctor')
                            <x-nav-link :href="route('queue.doctor-today')" :active="request()->routeIs('queue.doctor-today')">
                                {{ __('คิวตรวจวันนี้') }}
                            </x-nav-link>
                            <x-nav-link :href="route('queues.index')" :active="request()->routeIs('queues.index')">
                                {{ __('รายการคิวทั้งหมด') }}
                            </x-nav-link>
                        @endif

                        <!-- 2. เมนูเฉพาะ ADMIN -->
                        @if (strtolower(auth()->user()->role) === 'admin')
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('จัดการข้อมูลผู้ใช้') }}
                            </x-nav-link>
                        @endif

                        <!-- 3. เมนูสำหรับ STAFF และ ADMIN -->
                        @if (in_array(strtolower(auth()->user()->role), ['admin', 'staff']))
                            <x-nav-link :href="route('doctor-schedules.index')" :active="request()->routeIs('doctor-schedules.*')">
                                {{ __('จัดการตารางการทำงานของหมอ') }}
                            </x-nav-link>

                            <x-nav-link :href="route('queues.index')" :active="request()->routeIs('queues.*')">
                                {{ __('จัดการคิวเข้ารับบริการ') }}
                            </x-nav-link>

                            <!-- เมนูรายงาน (Dropdown) -->
                            <div class="hidden sm:flex sm:items-center">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out h-16 bg-transparent cursor-pointer">
                                            <div>รายงาน</div>
                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        @if (strtolower(auth()->user()->role) === 'admin')
                                            <x-dropdown-link href="{{ route('reports.users.pdf') }}">
                                                {{ __('รายงานบัญชีผู้ใช้ (PDF)') }}
                                            </x-dropdown-link>
                                        @endif

                                        <x-dropdown-link href="{{ route('queues.export-pdf', request()->query()) }}" target="_blank">
                                            {{ __('รายงานผู้เข้ารับบริการ (PDF)') }}
                                        </x-dropdown-link>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        @endif

                        <!-- 4. เมนูเฉพาะ PATIENT -->
                        @if (strtolower(auth()->user()->role) === 'patient')
                            <x-nav-link :href="route('doctor-schedules.index')" :active="request()->routeIs('doctor-schedules.index')">
                                {{ __('จองคิว') }}
                            </x-nav-link>
                            <x-nav-link :href="route('queue.history')" :active="request()->routeIs('queue.history')">
                                {{ __('ประวัติการจอง') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                {{ Auth::user()->name }}
                                <span class="ml-2 px-2 py-1 text-xs font-bold text-white rounded-full {{ strtolower(Auth::user()->role) === 'admin' ? 'bg-red-600' : (strtolower(Auth::user()->role) === 'staff' ? 'bg-blue-600' : (strtolower(Auth::user()->role) === 'doctor' ? 'bg-orange-500' : 'bg-green-600')) }}">
                                    {{ strtoupper(Auth::user()->role) }}
                                </span>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">โปรไฟล์</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">ออกจากระบบ</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Responsive Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('หน้าแรก') }}
                </x-responsive-nav-link>

                @if (strtolower(auth()->user()->role) === 'doctor')
                    <x-responsive-nav-link :href="route('queue.doctor-today')" :active="request()->routeIs('queue.doctor-today')">
                        {{ __('คิวตรวจวันนี้') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('queues.index')" :active="request()->routeIs('queues.index')">
                        {{ __('รายการคิวทั้งหมด') }}
                    </x-responsive-nav-link>
                @endif

                @if (strtolower(auth()->user()->role) === 'admin')
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('จัดการข้อมูลผู้ใช้') }}
                    </x-responsive-nav-link>
                @endif

                @if (in_array(strtolower(auth()->user()->role), ['admin', 'staff']))
                    <x-responsive-nav-link :href="route('doctor-schedules.index')" :active="request()->routeIs('doctor-schedules.*')">
                        {{ __('จัดการตารางการทำงานของหมอ') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('queues.index')" :active="request()->routeIs('queues.*')">
                        {{ __('จัดการคิวเข้ารับบริการ') }}
                    </x-responsive-nav-link>
                @endif

                @if (strtolower(auth()->user()->role) === 'patient')
                    <x-responsive-nav-link :href="route('doctor-schedules.index')" :active="request()->routeIs('doctor-schedules.index')">
                        {{ __('จองคิว') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('queue.history')" :active="request()->routeIs('queue.history')">
                        {{ __('ประวัติการจอง') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('โปรไฟล์') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('ออกจากระบบ') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>