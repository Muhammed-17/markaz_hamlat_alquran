<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

test('profile page is displayed', function () {
    // Create role and permission
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'edit profile', 'guard_name' => 'web']);
    $role->givePermissionTo('edit profile');

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = \Pest\Laravel\actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'edit profile', 'guard_name' => 'web']);
    $role->givePermissionTo('edit profile');

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = \Pest\Laravel\actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'edit profile', 'guard_name' => 'web']);
    $role->givePermissionTo('edit profile');

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = \Pest\Laravel\actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'edit profile', 'guard_name' => 'web']);
    $role->givePermissionTo('edit profile');

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = \Pest\Laravel\actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    expect(auth()->check())->toBeFalse();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Permission::firstOrCreate(['name' => 'edit profile', 'guard_name' => 'web']);
    $role->givePermissionTo('edit profile');

    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = \Pest\Laravel\actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});