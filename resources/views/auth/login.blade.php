<x-guest-layout>
    <div class="mb-4 flex justify-center">
        <a href="/">
            <img src="{{ asset('images/PNG Host.png') }}" alt="Logo" class="w-24 h-24 object-contain">
        </a>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('register') }}" class="text-sm text-green-600 hover:text-green-900 font-bold underline">
                {{ __('สมัครสมาชิกใหม่') }}
            </a>

            <div class="flex items-center">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md mr-4" href="{{ route('password.request') }}">
                        {{ __('ลืมรหัสผ่าน?') }}
                    </a>
                @endif

                <x-primary-button>
                    {{ __('เข้าสู่ระบบ') }}
                </x-primary-button>
            </div>
        </div>
    </form>
</x-guest-layout>