<?php

namespace App\Http\Controllers;

use App\Models\AudioBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Log;

class BookmarkController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'audioBookId' => 'required|exists:audio_books,id',
                'time' => 'required|integer|min:0',
            ]);

            // Save or update the bookmark
            $bookmark = Bookmark::updateOrCreate(
                [
                    'listener_id' => Auth::id(),
                    'audio_book_id' => $validated['audioBookId'],
                ],
                [
                    'time' => $validated['time'],
                ]
            );
            Log::info('AudioBookId received: ' . $request->audioBookId); // Log the received audioBookId

            // Return a success response
            return response()->json(['success' => true, 'message' => 'Bookmark saved successfully!']);
        } catch (\Exception $e) {

            Log::info('AudioBookId received: ' . $request->audioBookId); // Log the received audioBookId

            // Return a detailed error message
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
     //public function playAudio($audioBookId)
     //{
       // $bookmark = Bookmark::where('listener_id', Auth::id())
         //                    ->where('audio_book_id', $audioBookId)
           //                  ->first();

         //$startTime = $bookmark ? $bookmark->time : 0;  // Ensure 'time' column is used correctly
         //dd($startTime);  // Debug to check the value of $startTime

//         $audioBook = AudioBook::findOrFail($audioBookId);

  //       return view('listener.playAudio', compact('audioBook', 'startTime'));
    // }



}
