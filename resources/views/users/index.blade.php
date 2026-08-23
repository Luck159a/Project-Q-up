<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('จัดการข้อมูลผู้ใช้งาน') }}
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
            
            <div class="flex gap-2">
                {{-- ปุ่มดาวน์โหลด PDF ที่เพิ่มเข้ามาใหม่ (รองรับการกรองข้อมูล) --}}
                <a href="{{ route('admin.users.export-pdf', request()->all()) }}" 
                   target="_blank" 
                   class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 shadow-md transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    ดาวน์โหลด PDF
                </a>

                <a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-300 shadow-md">
                    + เพิ่มผู้ใช้งานใหม่
                </a>
            </div>
        </div>

        {{-- แสดงข้อความแจ้งเตือนเมื่อสำเร็จ --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        {{-- ระบบค้นหาผู้ใช้งาน --}}
        <form action="{{ route('users.index') }}" method="GET" class="mb-6">
            <div class="flex items-center space-x-2">
                <input type="text" name="search" placeholder="ค้นหาชื่อ, อีเมล หรือบทบาท..."
                    value="{{ request('search') }}"
                    class="flex-grow border border-gray-300 p-2 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                >
                <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">
                    ค้นหา
                </button>
                @if(request('search'))
                    <a href="{{ route('users.index') }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-300">ล้างค่า</a>
                @endif
            </div>
        </form>

        {{-- ตารางข้อมูลผู้ใช้งาน --}}
        <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-200">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr class="bg-gray-100 text-left text-gray-600 uppercase text-xs font-semibold tracking-wider">
                        <th class="px-5 py-4 border-b-2 border-gray-200">ลำดับ</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200">ชื่อ-นามสกุล</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200">อีเมล</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200">บทบาท (Role)</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200">สถานะ</th>
                        <th class="px-5 py-4 border-b-2 border-gray-200 text-center">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                <div class="font-bold text-gray-800">{{ $user->name }}</div>
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-bold text-white 
                                    {{ strtolower($user->role) === 'admin' ? 'bg-red-500' : 
                                       (strtolower($user->role) === 'staff' ? 'bg-blue-500' : 'bg-green-500') }}">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm">
                                @if($user->status === 'active')
                                    <span class="text-green-600 flex items-center">
                                        <span class="h-2 w-2 bg-green-600 rounded-full mr-2"></span> ใช้งานปกติ
                                    </span>
                                @elseif($user->status === 'banned')
                                    <span class="text-red-600 flex items-center">
                                        <span class="h-2 w-2 bg-red-600 rounded-full mr-2"></span> ระงับการใช้งาน
                                    </span>
                                @else
                                    <span class="text-gray-500 flex items-center">
                                        <span class="h-2 w-2 bg-gray-400 rounded-full mr-2"></span> {{ $user->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 border-b border-gray-200 text-sm text-center">
                                <div class="flex justify-center items-center space-x-3">
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium transition duration-150">แก้ไข</a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้นี้?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium transition duration-150">ลบ</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-500">ไม่พบข้อมูลผู้ใช้งาน</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</x-app-layout>