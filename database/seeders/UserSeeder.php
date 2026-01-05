<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // استيراد مودل المستخدم
use Illuminate\Support\Facades\Hash; // استيراد أداة تشفير كلمة المرور

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إنشاء حساب الأدمن
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'), // كلمة المرور هي "password"
            'role' => 'admin',
        ]);

        // 2. إنشاء حساب الناشر
        User::create([
            'name' => 'Publisher User',
            'email' => 'publisher@gmail.com',
            'password' => Hash::make('12345678'), // كلمة المرور هي "password"
            'role' => 'publisher',
        ]);

        // 3. إنشاء حساب المستمع
        User::create([
            'name' => 'Listener User',
            'email' => 'listener@gmail.com',
            'password' => Hash::make('12345678'), // كلمة المرور هي "password"
            'role' => 'listener',
        ]);
    }
}
