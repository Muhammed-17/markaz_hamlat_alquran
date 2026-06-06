<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL; // <-- تأكد من وجود هذا السطر
use Illuminate\Support\Facades\Vite; // <-- تأكد من وجود هذا السطر

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        \App\Models\Student::class => \App\Policies\StudentPolicy::class,
        \App\Models\Attendance::class => \App\Policies\AttendancePolicy::class,
        \App\Models\Subscription::class => \App\Policies\SubscriptionPolicy::class,
        \App\Models\Circle::class => \App\Policies\CirclePolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\SubscriptionPrice::class => \App\Policies\SubscriptionPricePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. تسجيل الـ Policies الخاصة بالـ Gates والصلاحيات
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // 2. حل مشكلة الـ CSS والتنسيقات المكسورة مع ngrok
        if (str_contains(request()->headers->get('host'), 'ngrok-free.dev')) {

            // إجبار Laravel على توليد كل الروابط ببروتوكول HTTPS الآمن
            URL::forceScheme('https');

            // إجبار أداة Vite على إرسال الـ Headers المناسبة عبر البروكسي
            Vite::useScriptTagAttributes([
                'crossorigin' => 'anonymous'
            ]);

            // تعيين مسار الـ Assets ليتطابق مع رابط ngrok المباشر للزائر
            config(['app.asset_url' => 'https://' . request()->headers->get('host')]);
        }
    }
}
