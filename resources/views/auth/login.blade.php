<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email / Mobile -->
        <div>
            <x-input-label for="email" :value="('البريد الإلكتروني')" />
            <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')" autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="('كلمة المرور')" />

            <div class="relative" x-data="{ showPassword: false }">
                <x-text-input id="password" class="block mt-1 w-full pe-10"
                    :type="'password'"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    name="password"
                    autocomplete="current-password" />

                <button type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 end-0 flex items-center pe-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg x-show="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div dir="rtl" class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember" checked>
                <span class="ms-2 text-sm text-gray-600">{{ ('تذكرني') }}</span>
            </label>
        </div>

        <div dir="rtl" class="flex items-center justify-end mt-4">
            {{-- @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
            {{ ('نسيت كلمة المرور؟') }}
            </a>
            @endif --}}

            <x-primary-button class="ms-3 rounded-xl">
                {{ ('تسجيل الدخول') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>