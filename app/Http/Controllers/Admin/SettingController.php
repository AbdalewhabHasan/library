<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * ▼▼▼ هذه هي الدالة الأولى التي سنبرمجها ▼▼▼
     * عرض صفحة الإعدادات مع جلب القيم الحالية من قاعدة البيانات.
     */
    public function index()
    {
        // جلب كل الإعدادات من قاعدة البيانات وتحويلها إلى صيغة سهلة الاستخدام
        // مثال: ['site_name' => 'مكتبتي', 'site_logo' => 'logo.png']
        $settings = Setting::pluck('value', 'key')->all();

        // عرض واجهة الإعدادات مع إرسال مصفوفة الإعدادات إليها
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * ▼▼▼ هذه هي الدالة الثانية التي سنبرمجها ▼▼▼
     * حفظ الإعدادات الجديدة القادمة من النموذج.
     */
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة
        $validatedData = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // صورة لا تتجاوز 2MB
        ]);

        // 2. المرور على كل البيانات القادمة من النموذج وحفظها
        foreach ($validatedData as $key => $value) {
            // استثناء حقل اللوجو لأنه يحتاج معاملة خاصة
            if ($key === 'site_logo') {
                continue;
            }
            
            // استخدام دالة updateOrCreate لتحديث القيمة إذا كانت موجودة، أو إنشائها إذا لم تكن موجودة
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // 3. التعامل مع رفع ملف اللوجو (إذا تم إرفاقه)
        if ($request->hasFile('site_logo')) {
            // حذف اللوجو القديم أولاً إذا كان موجوداً
            $oldLogo = Setting::where('key', 'site_logo')->first();
            if ($oldLogo && $oldLogo->value) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldLogo->value);
            }

            // رفع اللوجو الجديد وحفظ مساره
            $path = $request->file('site_logo')->store('logos', 'public');
            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $path]
            );
        }

        // 4. إعادة المستخدم إلى نفس الصفحة مع رسالة نجاح
      return redirect()->route('admin.dashboard')
                 ->with('success', 'تم حفظ الإعدادات بنجاح!');

    }
}
