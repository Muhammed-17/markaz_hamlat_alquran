<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\assertGuest;

uses(Tests\TestCase::class);

test('login screen can be rendered', function () {
    $response = get('/login');
    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    // 1. إنشاء الدور
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

    // 2. إنشاء المستخدم وتعيين الدور له
    $user = User::factory()->create();
    $user->assignRole('admin');

    // 3. إرسال طلب تسجيل الدخول
    $response = post('/login', [
        'email'    => $user->email,
        'password' => 'password',
    ]);

    // 4. التحقق من نجاح عملية المصادقة والتوجيه
    $this->assertTrue(auth()->check());
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertFalse(auth()->check());
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->post('/logout');

    assertGuest();
    $response->assertRedirect('/');
});
