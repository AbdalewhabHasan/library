<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

if (!function_exists('settings')) {
    /**
     * دالة مساعد لجلب قيمة إعداد معين.
     * تستخدم الكاش لتحسين الأداء وتقليل الاستعلامات على قاعدة البيانات.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function settings($key = null, $default = null)
    {
        // جلب كل الإعدادات من الكاش أو من قاعدة البيانات إذا لم تكن في الكاش
        $settings = Cache::rememberForever('app_settings', function () {
            return Setting::pluck('value', 'key')->all();
        });

        // إذا لم يتم طلب مفتاح معين، أرجع كل الإعدادات
        if (is_null($key)) {
            return $settings;
        }

        // إذا تم طلب مفتاح معين، أرجع قيمته
        return $settings[$key] ?? $default;
    }
}
