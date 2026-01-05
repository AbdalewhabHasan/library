@echo off
chcp 65001 >nul
title إصلاح تلقائي لجميع المشاكل
color 0B

echo.
echo ========================================
echo   🔧 إصلاح تلقائي (بدون تأكيد)
echo ========================================
echo.
echo ⚠️  سيتم حذف الكتب بدون ملفات تلقائياً
echo.

cd /d "%~dp0"

if not exist "C:\xampp\php\php.exe" (
    echo ❌ خطأ: PHP غير موجود
    pause
    exit /b 1
)

C:\xampp\php\php.exe FIX_EVERYTHING.php --auto

echo.
pause

