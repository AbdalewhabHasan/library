<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AudioBook;

class AllAudioBooksController extends Controller
{
    /**
     * عرض جميع الكتب الصوتية مع إمكانية البحث والترقيم.
     */
    public function index(Request $request)
    {
        // ابدأ ببناء استعلام لجلب الكتب الصوتية مع علاقاتها (الفئة والناشر)
        $query = AudioBook::query()->with('category', 'publisher');

        // فلترة الكتب بناءً على كلمة البحث إذا كانت موجودة
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('author', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // جلب جميع الكتب التي تطابق البحث، مع ترتيب الأحدث أولاً، وتقسيمها إلى صفحات
        $audioBooks = $query->latest()->paginate(12); // <-- نعرض 12 كتاباً في كل صفحة

        // إرسال البيانات إلى نفس الواجهة التي لديك
        return view('audio-books.all', compact('audioBooks'));
    }
}
