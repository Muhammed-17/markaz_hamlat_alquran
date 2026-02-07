<section>
    <header>
        <h2 class="text-xl font-bold text-[#0a5c36]">
            معلومات الملف الشخصي
        </h2>

        <p class="mt-2 text-sm text-gray-500 font-medium">
            قم بتحديث معلومات ملفك الشخصي وعنوان بريدك الإلكتروني.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-8 space-y-6">
        @csrf
        @method('patch')

        <div class="space-y-2">
            <x-input-label for="name" value="الاسم الكامل" class="text-right font-bold text-gray-700" />
            <x-text-input id="name" name="name" type="text"
                class="mt-1 block w-full border-gray-100 focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl p-4 transition-all"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-right" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-2">
            <x-input-label for="email" value="البريد الإلكتروني" class="text-right font-bold text-gray-700" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full border-gray-100 focus:border-emerald-500 focus:ring-emerald-500 rounded-2xl p-4 transition-all text-left"
                dir="ltr" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 text-right" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-amber-50 rounded-2xl border border-amber-100">
                    <p class="text-sm text-amber-800">
                        عنوان بريدك الإلكتروني غير محقق.

                        <button form="send-verification"
                            class="underline text-sm text-amber-900 hover:text-amber-700 font-bold rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            انقر هنا لإعادة إرسال بريد التحقق.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-sm text-emerald-600">
                            تم إرسال رابط تحقق جديد إلى عنوان بريدك الإلكتروني.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit"
                class="px-8 py-4 bg-[#0a5c36] text-white rounded-2xl font-black text-lg hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-emerald-900/10">
                حفظ التغييرات
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-600 font-bold">تم الحفظ بنجاح.</p>
            @endif
        </div>
    </form>
</section>
