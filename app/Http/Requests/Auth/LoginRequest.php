<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginValue = $this->input('email');
        $isNumeric  = preg_match('/^[0-9+\- ]+$/', $loginValue);

        $user = null;

        if ($isNumeric) {
            $user = User::where('mobile', $loginValue)->first()
                ?? User::where('email', $loginValue)->first();
        } else {
            $user = User::where('email', $loginValue)->first();
        }

        // ✅ دايماً اعمل hash check لمنع timing attacks
        $dummyHash = '$2y$12$invalidhashfortimingattackpreventioniiiiiiiiiiiiiiiiii';

        if (!$user || !Hash::check($this->input('password'), $user->password ?? $dummyHash)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'بيانات الدخول غير صحيحة',
            ]);
        }

        // ✅ تحقق من الـ status بعد التحقق من الباسورد
        if ($user->status === 'inactive') {
            // ✅ hit the rate limiter لمنع enumerate الحسابات المعطلة
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'email' => 'تم تعطيل حسابك. تواصل مع الإدارة.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(
            Str::lower($this->string('email')) . '|' . $this->ip()
        );
    }
}
