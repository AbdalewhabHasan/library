<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function subscribe(User $publisher)
    {
        /** @var \App\Models\User $listener */
        $listener = Auth::user();
        $listener->subscriptions()->syncWithoutDetaching([$publisher->id]);
        return back()->with('success', 'تم الاشتراك بنجاح في ' . $publisher->name);
    }

    public function unsubscribe(User $publisher)
    {
        /** @var \App\Models\User $listener */
        $listener = Auth::user();
        $listener->subscriptions()->detach($publisher->id);
        return back()->with('success', 'تم إلغاء الاشتراك من ' . $publisher->name);
    }
}
