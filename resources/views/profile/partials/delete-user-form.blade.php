<section class="space-y-6">
    <header>
        <h2 class="text-xl font-bold text-red-600">
            حذف الحساب
        </h2>

        <p class="mt-2 text-sm text-gray-500 font-medium">
            بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته بشكل دائم. قبل حذف حسابك، يرجى تنزيل أي بيانات أو معلومات
            ترغب في الاحتفاظ بها.
        </p>
    </header>

    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-8 py-4 bg-red-600 text-white rounded-2xl font-black text-lg hover:bg-red-700 transition-all shadow-lg shadow-red-900/10">
        حذف الحساب نهائياً
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
            @csrf
            @method('delete')

            <h2 class="text-xl font-bold text-gray-800 text-right">
                هل أنت متأكد من رغبتك في حذف الحساب؟
            </h2>

            <p class="mt-4 text-sm text-gray-500 text-right leading-relaxed">
                بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته بشكل دائم. يرجى إدخال كلمة المرور الخاصة بك لتأكيد رغبتك
                في حذف حسابك نهائياً.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="كلمة المرور" class="sr-only" />

                <x-text-input id="password" name="password" type="password"
                    class="mt-1 block w-full border-gray-100 focus:border-red-500 focus:ring-red-500 rounded-2xl p-4 transition-all text-left"
                    dir="ltr" placeholder="كلمة المرور" />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-right" />
            </div>

            <div class="mt-8 flex justify-start gap-4 flex-row-reverse">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-6 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-all">
                    إلغاء
                </button>

                <button type="submit"
                    class="px-6 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition-all">
                    تأكيد حذف الحساب
                </button>
            </div>
        </form>
    </x-modal>
</section>
