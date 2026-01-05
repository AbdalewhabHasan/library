@echo off
chcp 65001 >nul
echo ========================================
echo   حذف الكتب التي لا تملك ملفات
echo ========================================
echo.

cd /d "%~dp0"

if exist "C:\xampp\php\php.exe" (
    echo جاري فحص الكتب...
    echo.
    C:\xampp\php\php.exe check_and_delete_missing_books.php
) else (
    echo خطأ: PHP غير موجود في C:\xampp\php\php.exe
    echo يرجى تحديث المسار في هذا الملف
)

echo.
pause

