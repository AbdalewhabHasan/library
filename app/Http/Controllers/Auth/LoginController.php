<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // <-- تم إضافة هذا السطر لاستخدام Request

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // |                                                           |
    // |            هذا هو الكود الجديد الذي تمت إضافته            |
    // |                                                           |
    // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        // التحقق من دور المستخدم الذي قام بتسجيل الدخول
        if ($user->role === 'admin') {
            // ملاحظة: سنقوم بإنشاء هذا المسار المسمى لاحقاً
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'publisher') {
            // ملاحظة: تأكد من أن لديك مساراً بهذا الاسم للناشر
            return redirect()->route('publisher.dashboard');
        }

        // إذا لم يكن أدمن أو ناشر، سيتم استخدام المسار الافتراضي $redirectTo
        return redirect($this->redirectTo);
    }
}