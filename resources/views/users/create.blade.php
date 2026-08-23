<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('สร้างบัญชีผู้ใช้') }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">✨ Create New User</h1>

    <div class="max-w-xl mx-auto">
        <form action="{{ route('users.store') }}" method="POST" class="bg-white shadow-xl rounded-lg p-8">
            @csrf

            <div class="mb-5">
                <label for="name" class="block text-gray-700 font-bold mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150"
                       placeholder="Enter user name">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label for="email" class="block text-gray-700 font-bold mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150"
                       placeholder="user@example.com">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label for="password" class="block text-gray-700 font-bold mb-2">Password <span class="text-red-500">*</span></label>
                <input type="password" id="password" name="password" required
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150"
                       placeholder="********">
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-5">
                <label for="password_confirmation" class="block text-gray-700 font-bold mb-2">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150"
                       placeholder="********">
            </div>
            
            <div class="mb-5">
                <label for="role" class="block text-gray-700 font-bold mb-2">Role <span class="text-red-500">*</span></label>
                <select id="role" name="role" required
                       class="shadow border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
                    <option value="Patient" {{ old('role') == 'Patient' ? 'selected' : '' }}>Patient</option>
                    <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>Staff</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Doctor" {{ old('role') == 'Doctor ' ? 'selected' : '' }}>Doctor </option>
                </select>
                @error('role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="mb-6">
                <label for="status" class="block text-gray-700 font-bold mb-2">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                       class="shadow border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300 shadow-md focus:outline-none focus:shadow-outline">
                    Create User
                </button>
                <a href="{{ route('users.index') }}" class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-800 transition duration-150">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>