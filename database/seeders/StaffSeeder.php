<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staffMembers = [
            [
                'name' => 'سعد أحمد سعد الشعراوي',
                'email' => '01212345678@markaz.com',
                'role' => 'supervisor', // مشرف
                'password' => 'password',
            ],
            [
                'name' => 'عبدالفتاح أحمد سعدون',
                'email' => '01150175090@markaz.com',
                'role' => 'teacher', // معلم
                'password' => 'password',
            ],
            [
                'name' => 'عبدالبديع أبوالمعاطي',
                'email' => 'adbelbadea@markaz.com',
                'role' => 'supervisor', // مشرف
                'password' => 'password',
            ]
        ];

        foreach ($staffMembers as $member) {
            // Create or update the user
            $user = User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make($member['password']),
                    'status' => 'active' // assuming status exists
                ]
            );

            // Assign role
            if (!$user->hasRole($member['role'])) {
                $user->assignRole($member['role']);
            }

            // Create teacher record
            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                ['name' => $user->name]
            );
        }
    }
}
