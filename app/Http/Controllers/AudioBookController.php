<?php

namespace App\Http\Controllers;

use App\Models\AudioBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AudioBookController extends Controller
{
    /**
     * عرض كل الكتب الصوتية الخاصة بالناشر المسجل.
     */
    public function index()
    {
        $audioBooks = AudioBook::where('publisher_id', Auth::id())
                                ->latest()
                                ->get();

        return view('publisher.audio-books.index', compact('audioBooks'));
    }

    /**
     * عرض صفحة تعديل الكتاب.
     */
    public function edit(AudioBook $audioBook)
    {
        if ($audioBook->publisher_id !== Auth::id()) {
            return redirect()->route('publisher.audio-books.index')->with('error', 'You are not authorized to edit this audio book.');
        }
        return view('publisher.audio-books.edit', compact('audioBook'));
    }

    /**
     * تحديث الكتاب في قاعدة البيانات.
     */
    public function update(Request $request, AudioBook $audioBook)
    {
        if ($audioBook->publisher_id !== Auth::id()) {
            return redirect()->route('publisher.audio-books.index')->with('error', 'You are not authorized to update this audio book.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $audioBookData = $request->except(['file', 'cover_image']);
        $audioBookData['status'] = 'pending';

        $audioBook->update($audioBookData);

        if ($request->hasFile('file')) {
            if ($audioBook->file_path) {
                Storage::disk('public')->delete($audioBook->file_path);
            }
            $filePath = $request->file('file')->store('audio_books', 'public');
            $audioBook->file_path = $filePath;
        }

        if ($request->hasFile('cover_image')) {
            if ($audioBook->cover_image_path) {
                Storage::disk('public')->delete($audioBook->cover_image_path);
            }
            $coverImagePath = $request->file('cover_image')->store('cover_images', 'public');
            $audioBook->cover_image_path = $coverImagePath;
        }
        
        $audioBook->save();

        return redirect()->route('publisher.audio-books.index')->with('success', 'تم تحديث الكتاب وإرساله للمراجعة مجدداً.');
    }

    /**
     * حذف الكتاب من قاعدة البيانات.
     */
    public function destroy(AudioBook $audioBook)
    {
        if ($audioBook->publisher_id !== Auth::id()) {
            return redirect()->route('publisher.audio-books.index')->with('error', 'You are not authorized to delete this audio book.');
        }

        if ($audioBook->cover_image_path) {
            Storage::disk('public')->delete($audioBook->cover_image_path);
        }
        if ($audioBook->file_path) {
            Storage::disk('public')->delete($audioBook->file_path);
        }

        $audioBook->delete();

        return redirect()->route('publisher.audio-books.index')->with('success', 'Audio book deleted successfully!');
    }
    
    /**
     * عرض صفحة إضافة كتاب.
     */
    public function create()
    {
        return view('publisher.audio-books.create');
    }

    /**
     * عرض كل الكتب لكل المستخدمين (للبحث العام).
     */
    // ابحث عن هذه الدالة في AudioBookController.php
public function allAudioBooks(Request $request)
{
    // 1. جلب كل الفئات لعرضها في الفلتر (هذا هو السطر الذي كان ناقصاً)
    $categories = \App\Models\Category::orderBy('name')->get();

    // 2. ابدأ ببناء استعلام الكتب المقبولة فقط
    $query = \App\Models\AudioBook::where('status', 'approved')->with(['publisher', 'category']);

    // 3. طبق فلتر البحث إذا كان موجوداً
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
              ->orWhere('author', 'LIKE', "%{$searchTerm}%");
        });
    }

    // 4. طبق فلتر الفئة إذا كان موجوداً
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // 5. نفذ الاستعلام مع الترقيم
    $audioBooks = $query->latest()->paginate(12);

    // 6. إرسال كل من الكتب والفئات إلى الصفحة
    return view('publisher.audio-books.all', compact('audioBooks', 'categories'));
}

}
