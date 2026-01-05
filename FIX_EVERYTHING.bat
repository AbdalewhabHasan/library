@echo off
chcp 65001 >nul
title إصلاح جميع مشاكل النظام
color 0A

echo.
echo ========================================
echo   🔧 إصلاح جميع مشاكل النظام
echo ========================================
echo.

cd /d "%~dp0"

if not exist "C:\xampp\php\php.exe" (
    echo ❌ خطأ: PHP غير موجود في C:\xampp\php\php.exe
    echo.
    echo يرجى تحديث المسار في هذا الملف
    echo.
    pause
    exit /b 1
)

echo جاري تشغيل السكريبت الشامل...
echo.
echo ⏳ يرجى الانتظار...
echo.

C:\xampp\php\php.exe FIX_EVERYTHING.php

echo.
echo ========================================
echo   ✅ انتهى!
echo ========================================
echo.
pause

