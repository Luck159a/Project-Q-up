@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">👤 User Details: {{ $user->name }}</h1>

    <div class="bg-white shadow-lg rounded-lg p-6 max-w-lg mx-auto">
        <div class="border-b pb-4 mb-4">
            <p class="text-sm text-gray-500">ID:</p>
            <p class="text-lg font-semibold text-gray-900">{{ $user->id }}</p>
        </div>
        <div class="border-b pb-4 mb-4">
            <p class="text-sm text-gray-500">Name:</p>
            <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
        </div>
        <div class="border-b pb-4 mb-4">
            <p class="text-sm text-gray-500">Email:</p>
            <p class="text-lg font-semibold text-gray-900">{{ $user->email }}</p>
        </div>
        <div class="border-b pb-4 mb-4">
            <p class="text-sm text-gray-500">Role:</p>
            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($user->role) }}</p>
        </div>
        <div class="border-b pb-4 mb-4">
            <p class="text-sm text-gray-500">Status:</p>
            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($user->status) }}</p>
        </div>
        <div class="border-b pb-4 mb-4">
            <p class="text-sm text-gray-500">Created At:</p>
            <p class="text-lg font-semibold text-gray-900">{{ $user->created_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Updated At:</p>
            <p class="text-lg font-semibold text-gray-900">{{ $user->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
    
    <div class="flex justify-center mt-6">
        <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300 shadow-md">
            ← Back to List
        </a>
        <a href="{{ route('users.edit', $user->id) }}" class="ml-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition duration-300 shadow-md">
            Edit User
        </a>
    </div>
</div>
@endsection