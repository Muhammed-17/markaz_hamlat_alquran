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
    // Create role and permission
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'view dashboard', 'guard_name' => 'web']);
    $role->givePermissionTo('view dashboard');

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

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
