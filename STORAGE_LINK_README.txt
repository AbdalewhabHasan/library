===========================================
حل مشكلة Storage Link في Laravel
===========================================

المشكلة:
الملفات المخزنة في storage/app/public لا تظهر في الواجهات

الحل:
يجب إنشاء رابط رمزي (Symbolic Link) من public/storage إلى storage/app/public

الطريقة 1: استخدام Artisan (الأفضل)
-----------------------------------
افتح Command Prompt أو PowerShell كـ Administrator، ثم نفذ:

    C:\xampp\php\php.exe artisan storage:link

الطريقة 2: استخدام الملفات المساعدة
-----------------------------------
1. انقر نقراً مزدوجاً على: fix_storage.bat
   (قد تحتاج لتشغيله كـ Administrator - اضغط زر الفأرة الأيمن واختر "Run as administrator")

2. أو نفذ: C:\xampp\php\php.exe create_storage_link.php

ملاحظة مهمة:
------------
في Windows، قد تحتاج صلاحيات Administrator لإنشاء Symbolic Links.

بعد إنشاء الرابط:
-----------------
- جميع الملفات في storage/app/public ستكون متاحة عبر public/storage
- الصور والملفات الصوتية ستظهر في الواجهات
- يمكنك التحقق بفتح: http://localhost/library/public/storage

===========================================

