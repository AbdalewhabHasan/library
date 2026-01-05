<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('achievements')->insert([
            // --- إنجازات تعتمد على عدد الكتب المستمع إليها ---
            [
                'name' => 'مستكشف جديد',
                'description' => 'أكملت أول كتاب لك!',
                'icon' => 'fa-solid fa-flag',
                'type' => 'listen_count',
                'value' => 1,
                'related_id' => null,
            ],
            [
                'name' => 'قارئ نهم',
                'description' => 'استمعت إلى 10 كتب مختلفة.',
                'icon' => 'fa-solid fa-book-reader',
                'type' => 'listen_count',
                'value' => 10,
                'related_id' => null,
            ],

            // --- إنجازات تعتمد على فئة معينة (افترض أن ID فئة "التاريخ" هو 1) ---
            [
                'name' => 'مؤرخ مبتدئ',
                'description' => 'استمعت إلى 3 كتب تاريخية.',
                'icon' => 'fa-solid fa-landmark',
                'type' => 'category_count',
                'value' => 3,       // عدد الكتب المطلوب
                'related_id' => 1,  // ID الفئة (التاريخ)
            ],
            [
                'name' => 'خبير تطوير الذات',
                'description' => 'استمعت إلى 5 كتب في تطوير الذات.',
                'icon' => 'fa-solid fa-brain',
                'type' => 'category_count',
                'value' => 5,       // عدد الكتب المطلوب
                'related_id' => 2,  // افترض أن ID فئة "تطوير الذات" هو 2
            ],
        ]);
    }
}
