<section>
    <header>
        <h2 class="text-xl font-bold text-[#0a5c36]">
            تحديث كلمة المرور
        </h2>

        <p class="mt-2 text-sm text-gray-500 font-medium">
            تأكد من استخدام كلمة مرور طويلة وعشوائية للحفاظ على أمان حسابك.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('put')

        <div class="space-y-2">
            <x-input-label for="update_password_current_password" value="كلمة المرور الحالية"
                class="text-right font-bold text-gray-700" />
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full border-gray-100 focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl p-4 transition-all text-left"
                dir="ltr" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-right" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password" value="كلمة المرور الجديدة"
                class="text-right font-bold text-gray-700" />
            <x-text-input id="update_password_password" name="password" type="password"
                class="mt-1 block w-full border-gray-100 focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl p-4 transition-all text-left"
                dir="ltr" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-right" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password_confirmation" value="تأكيد كلمة المرور الجديدة"
                class="text-right font-bold text-gray-700" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full border-gray-100 focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl p-4 transition-all text-left"
                dir="ltr" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-right" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit"
                class="px-8 py-4 bg-[#0a5c36] text-white rounded-2xl font-black text-lg hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-emerald-900/10">
                تحديث كلمة المرور
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-600 font-bold">تم التحديث بنجاح.</p>
            @endif
        </div>
    </form>
</section>
