<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * تحديد ما إذا كان المستخدم (الناشر) يمكنه حذف التعليق.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // السماح بالحذف فقط إذا كان دور المستخدم هو "ناشر"
        // و إذا كان معرف الناشر في الكتاب الصوتي للتعليق هو نفس معرف المستخدم الحالي
        return $user->role === 'publisher' && $comment->audioBook->publisher_id === $user->id;
    }
}
