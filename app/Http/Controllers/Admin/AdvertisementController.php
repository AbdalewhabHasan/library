<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::latest()->get();
        return view('admin.advertisements.index', compact('advertisements'));
    }

    public function create()
    {
        return view('admin.advertisements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'link_url' => 'required|url',
        ]);

        $imagePath = $request->file('image')->store('advertisements', 'public');

        Advertisement::create([
            'image_path' => $imagePath,
            'link_url' => $request->link_url,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.advertisements.index')->with('success', 'تمت إضافة الإعلان بنجاح.');
    }

    public function destroy(Advertisement $advertisement)
    {
        Storage::disk('public')->delete($advertisement->image_path);
        $advertisement->delete();
        return back()->with('success', 'تم حذف الإعلان بنجاح.');
    }
}
