<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'مركز حملة القرآن',
            'email' => 'markaz@gmail.com',
            'password' => bcrypt('172021m'),
            'status' => 'active',
        ]);
        $user->assignRole('admin');

        $this->command->info('✅ تم إنشاء حساب المدير بنجاح.');
    }
}