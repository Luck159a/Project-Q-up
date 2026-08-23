<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit') }}
        </h2>
    </x-slot>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">✏️ Edit User: {{ $user->name }}</h1>

    <form action="{{ route('users.update', $user->id) }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>
        
        <div class="mb-4">
            <label for="role" class="block text-gray-700 font-bold mb-2">Role</label>
            <select id="role" name="role" required
                   class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="Patient" {{ old('role') == 'Patient' ? 'selected' : '' }}>Patient</option>
                    <option value="Staff" {{ old('role') == 'Staff' ? 'selected' : '' }}>Staff</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Doctor" {{ old('role') == 'Doctor ' ? 'selected' : '' }}>Doctor </option>
            </select>
            @error('role') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>
        
        <div class="mb-4">
            <label for="status" class="block text-gray-700 font-bold mb-2">Status</label>
            <select id="status" name="status" required
                   class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="banned" {{ old('status', $user->status) == 'banned' ? 'selected' : '' }}>Banned</option>
            </select>
            @error('status') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block text-gray-700 font-bold mb-2">New Password (Leave blank to keep current)</label>
            <input type="password" id="password" name="password"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('password') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>
        
        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 font-bold mb-2">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300 focus:outline-none focus:shadow-outline">
                Update User
            </button>
            <a href="{{ route('users.index') }}" class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-800">
                Cancel
            </a>
        </div>
    </form>
</div>
</x-app-layout>