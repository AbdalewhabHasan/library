<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // ▼▼▼ أي مستخدم جديد يتم تسجيله يكون "مستمع" بشكل افتراضي ▼▼▼
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'listener', // <-- تم تعيين الدور افتراضياً هنا
        ]);

        event(new Registered($user));

        Auth::login($user);

        // بما أن كل المسجلين الجدد مستمعون، نوجههم دائماً للوحة تحكم المستمع
        return redirect()->route('listener.dashboard');
    }
}
