<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ▼▼▼ هذا هو السطر الوحيد الذي تحتاجه هنا ▼▼▼
        // هذا السطر يستدعي ملف البذار الذي أنشأناه، والذي سيقوم ببناء
        // حسابات الأدمن والناشر والمستمع.
        $this->call(UserSeeder::class);
         $this->call(AchievementSeeder::class);
    }
}
