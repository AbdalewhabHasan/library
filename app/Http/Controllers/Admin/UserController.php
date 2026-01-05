<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Notification;

class UserController extends Controller
{
    /**
     * عرض صفحة تحتوي على جدول بجميع المستخدمين.
     */
    public function index(Request $request)
    {
        $query = User::where('id', '!=', Auth::id());

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($subQuery) use ($searchTerm) {
                $subQuery->where('name', 'like', '%' . $searchTerm . '%')
                         ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('role') && in_array($request->role, ['publisher', 'listener', 'admin'])) {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && in_array($request->status, ['active', 'banned'])) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15)->appends($request->except('page'));

        return view('admin.users.index', compact('users'));
    }
    
    /**
     * عرض فورم إنشاء مستخدم جديد.
     */
    public function create()
    {
        $roles = ['listener', 'publisher', 'admin'];
        return view('admin.users.create', compact('roles'));
    }
    
    /**
     * حفظ المستخدم الجديد وإرسال إشعار.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:listener,publisher,admin'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        // --- إرسال إشعار (بالطريقة الصحيحة النهائية 100%) ---
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'notifiable_id' => $admin->id,
                'notifiable_type' => \App\Models\User::class,
                'type' => 'NewUserRegistered', // <-- هذا هو السطر الحاسم الذي يحل المشكلة
                'data' => [
                    'message' => "تم تسجيل مستخدم جديد: {$user->name} بدور '{$user->role}'.",
                    'link' => route('admin.users.edit', $user->id),
                    'icon' => 'fas fa-user-plus',
                ]
            ]);
        }
        // --- انتهى جزء الإشعار ---

        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح!');
    }

    /**
     * عرض صفحة تعديل بيانات مستخدم محدد.
     */
    public function edit(User $user)
    {
        $roles = ['listener', 'publisher', 'admin'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * تحديث بيانات المستخدم وإرسال إشعار عند الترقية.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:listener,publisher,admin',
        ]);

        $originalRole = $user->getOriginal('role');

        $user->update($request->all());

        // --- إشعار عند الترقية (بالطريقة الصحيحة النهائية 100%) ---
        if ($originalRole !== 'publisher' && $user->role === 'publisher') {
            $admins = User::where('role', 'admin')->where('id', '!=', Auth::id())->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'notifiable_id' => $admin->id,
                    'notifiable_type' => \App\Models\User::class,
                    'type' => 'UserPromotedToPublisher', // <-- هذا هو السطر الحاسم الذي يحل المشكلة
                    'data' => [
                        'message' => "تم ترقية المستخدم {$user->name} إلى دور 'ناشر'.",
                        'link' => route('admin.users.edit', $user->id),
                        'icon' => 'fas fa-user-shield',
                    ]
                ]);
            }
        }
        // --- انتهى جزء الإشعار ---

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح!');
    }

    /**
     * تقوم هذه الدالة بقلب حالة المستخدم بين 'active' و 'banned'.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'لا يمكنك تغيير حالة حسابك الخاص.');
        }

        $user->status = ($user->status === 'active') ? 'banned' : 'active';
        $user->save();

        $message = ($user->status === 'banned') ? 'تم حظر المستخدم بنجاح.' : 'تم تفعيل حساب المستخدم بنجاح.';

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    /**
     * حذف مستخدم محدد من قاعدة البيانات.
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح!');
    }
    
    /**
     * عرض تفاصيل مستخدم محدد (حالياً يعيد التوجيه للتعديل).
     */
    public function show(User $user)
    {
        return redirect()->route('admin.users.edit', $user->id);
    }
}
