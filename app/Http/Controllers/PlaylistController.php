<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Playlist;
use App\Models\AudioBook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PlaylistController extends Controller
{
    /**
     * Display a listing of the resource for the authenticated user.
     */
    public function index(Request $request)
    {
        $listenerId = Auth::id();
        $query = Playlist::where('listener_id', $listenerId);

        $search = $request->input('search');
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $playlists = $query->latest()->get();

        // يفترض أن يكون لديك ملف عرض في هذا المسار: listener/playlists/index.blade.php
        // إذا كان اسمه مختلفاً، يجب تعديله هنا.
        return view('listener.playlists.index', compact('playlists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ▼▼▼ تم إرجاع هذا السطر ليتوافق مع اسم ملفك الحالي ▼▼▼
        return view('listener.createPlaylist');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'required|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $playlist = new Playlist();
        $playlist->listener_id = Auth::id();
        $playlist->name = $request->name;
        $playlist->description = $request->description;
        $playlist->is_public = $request->is_public;

        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('playlist_covers', 'public');
            $playlist->cover_image = $imagePath;
        }

        $playlist->save();

        // ▼▼▼ تم تصحيح اسم المسار هنا ليتوافق مع المسارات المخصصة ▼▼▼
        return redirect()->route('listener.playlists.index')->with('success', 'تم إنشاء قائمة التشغيل بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Playlist $playlist)
    {
        if ($playlist->listener_id !== Auth::id() && !$playlist->is_public) {
            abort(403, 'لا تملك صلاحية لعرض قائمة التشغيل هذه.');
        }

        return view('listener.playlists.show', compact('playlist'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Playlist $playlist)
    {
        if ($playlist->listener_id !== Auth::id()) {
            return redirect()->route('listener.playlists.index')->with('error', 'لا يمكنك حذف قائمة التشغيل هذه لأنها لا تخصك.');
        }

        if ($playlist->cover_image) {
            Storage::disk('public')->delete($playlist->cover_image);
        }

        $playlist->audioBooks()->detach();
        $playlist->delete();

        return redirect()->route('listener.playlists.index')->with('success', 'تم حذف قائمة التشغيل بنجاح.');
    }

    /**
     * Add audio book to a playlist.
     */
    public function addAudio(Request $request)
    {
        $request->validate([
            'playlistId' => 'required|exists:playlists,id',
            'audioBookId' => 'required|exists:audio_books,id',
        ]);

        $playlist = Playlist::find($request->playlistId);

        if ($playlist->listener_id !== Auth::id()) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية للإضافة إلى قائمة التشغيل هذه.');
        }

        if (!$playlist->audioBooks->contains($request->audioBookId)) {
            $playlist->audioBooks()->attach($request->audioBookId);
            return redirect()->back()->with('success', 'تمت إضافة الكتاب الصوتي إلى قائمة التشغيل بنجاح.');
        } else {
            return redirect()->back()->with('warning', 'الكتاب الصوتي موجود بالفعل في قائمة التشغيل هذه.');
        }
    }

    /**
     * Remove audio book from a playlist.
     */
    public function removeAudioBook(Playlist $playlist, AudioBook $audioBook)
    {
        if ($playlist->listener_id !== Auth::id()) {
            return redirect()->back()->with('error', 'ليس لديك صلاحية لتعديل قائمة التشغيل هذه.');
        }
        
        $playlist->audioBooks()->detach($audioBook->id);

        // ▼▼▼ تم تصحيح اسم المسار هنا ليتوافق مع المسارات المخصصة ▼▼▼
        return redirect()->route('listener.playlists.show', $playlist->id)
            ->with('success', 'تمت إزالة الكتاب الصوتي من قائمة التشغيل بنجاح.');
    }
}
