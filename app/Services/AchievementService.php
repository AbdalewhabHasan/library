<?php

namespace App\Services;

use App\Models\User;
use App\Models\Achievement;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    /**
     * يتحقق من كل الإنجازات المتاحة للمستخدم ويمنحها له.
     * @param User $listener المستخدم الذي يتم التحقق من إنجازاته.
     * @return \Illuminate\Support\Collection مجموعة من الإنجازات الجديدة التي تم فتحها.
     */
    public function checkAndAwardAchievements(User $listener)
    {
        // جلب كل الإنجازات التي لم يحصل عليها المستخدم بعد
        $unlockedAchievementIds = $listener->achievements()->pluck('achievements.id');
        $achievementsToCheck = Achievement::whereNotIn('id', $unlockedAchievementIds)->get();

        $newlyAwarded = collect(); // لجمع الإنجازات الجديدة فقط

        foreach ($achievementsToCheck as $achievement) {
            $unlocked = false;
            switch ($achievement->type) {
                case 'listen_count':
                    $unlocked = $this->checkListenCount($listener, $achievement->value);
                    break;

                case 'category_count':
                    // نفترض أن قيمة الإنجاز هنا هي ID الفئة
                    // سنحتاج إلى إضافة عمود 'related_id' لجدول achievements
                    $unlocked = $this->checkCategoryCount($listener, $achievement->value, $achievement->related_id ?? 0);
                    break;
            }

            if ($unlocked) {
                // امنح الإنجاز للمستخدم
                $listener->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
                $newlyAwarded->push($achievement); // أضف الإنجاز الجديد إلى القائمة
            }
        }

        return $newlyAwarded;
    }

    /**
     * يتحقق مما إذا كان المستخدم قد استمع إلى عدد معين من الكتب.
     */
    private function checkListenCount(User $listener, int $requiredCount): bool
    {
        $listenCount = $listener->listeningHistory()->distinct('audio_book_id')->count();
        return $listenCount >= $requiredCount;
    }

    /**
     * يتحقق مما إذا كان المستخدم قد استمع إلى عدد معين من الكتب في فئة معينة.
     * @param int $requiredCount عدد الكتب المطلوب.
     * @param int $categoryId رقم الفئة المطلوب التحقق منها.
     */
    private function checkCategoryCount(User $listener, int $requiredCount, int $categoryId): bool
    {
        if ($categoryId === 0) return false; // لا تقم بالتحقق إذا لم يكن هناك ID للفئة

        $categoryListenCount = $listener->listeningHistory()
            ->join('audio_books', 'listening_histories.audio_book_id', '=', 'audio_books.id')
            ->where('audio_books.category_id', $categoryId)
            ->distinct('audio_book_id')
            ->count();

        return $categoryListenCount >= $requiredCount;
    }
}
