<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\AudioBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // Add a comment
// في ملف app/Http/Controllers/CommentController.php

// في ملف app/Http/Controllers/CommentController.php

public function addComment(Request $request, AudioBook $audioBook)
{
    // 1. التحقق من صحة البيانات القادمة
    $validated = $request->validate([
        'comment' => 'required|string|max:1000'
    ]);

    // 2. إنشاء التعليق وربطه بالمستخدم والكتاب
    $comment = $audioBook->comments()->create([
        'listener_id' => Auth::id(),
        'comment' => $validated['comment'],
    ]);

    // 3. تحميل بيانات المستخدم مع التعليق لإرجاعها
    $comment->load('user');

    // 4. التحقق إذا كان الطلب من نوع AJAX/JSON (الأهم)
    if ($request->expectsJson()) {
        // إذا كان كذلك، أرجع رد JSON بالبيانات الجديدة
        return response()->json(['success' => true, 'comment' => $comment]);
    }

    // 5. إذا كان الطلب عادياً (بدون JavaScript)، قم بإعادة التوجيه
    return back()->with('success', 'تمت إضافة تعليقك بنجاح.');
}


    // Show comments for a specific audiobook
    public function showComments(AudioBook $audioBook)
    {
        // Eager load comments and the user associated with them
        $audioBook->load('comments.user');

        // This will now correctly look for 'resources/views/listener/comments.blade.php'
        return view('listener.comments', compact('audioBook'));
    }

    // Display the edit form
    public function edit(Comment $comment)
    {
        // Ensure the user is the owner of the comment
        if ($comment->listener_id !== Auth::id()) {
            return redirect()->back()->with('warning', 'You are not authorized to edit this comment.');
        }

        // Make sure you have a view at 'resources/views/listener/editComment.blade.php'
        return view('listener.editComment', compact('comment'));
    }

    // Update the comment
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        // Ensure the user is the owner of the comment
        if ($comment->listener_id !== Auth::id()) {
            return redirect()->back()->with('warning', 'You are not authorized to update this comment.');
        }

        $comment->update([
            'comment' => $request->comment,
        ]);

        // Redirect back to the comments page for that audiobook
        return redirect()->route('listener.comments.show', $comment->audio_book_id)
                         ->with('success', 'Comment updated successfully!');
    }

    // Delete the comment
    public function destroy(Comment $comment)
    {
        // Ensure the user is the owner of the comment
        if ($comment->listener_id !== Auth::id()) {
            return redirect()->back()->with('warning', 'You are not authorized to delete this comment.');
        }

        $audioBookId = $comment->audio_book_id;
        $comment->delete();

        // Redirect back to the comments page for that audiobook
        return redirect()->route('listener.comments.show', $audioBookId)
                         ->with('success', 'Comment deleted successfully!');
    }
}
