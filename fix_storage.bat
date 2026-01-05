@echo off
chcp 65001 >nul
echo ========================================
echo   Creating Storage Symbolic Link
echo ========================================
echo.

:: Check for admin privileges
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo Requesting Administrator privileges...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

cd /d "%~dp0"
echo Current directory: %CD%
echo.

:: Check if PHP exists
set "PHP_PATH=C:\xampp\php\php.exe"
if not exist "%PHP_PATH%" (
    echo Error: PHP not found at %PHP_PATH%
    echo.
    echo Please update PHP_PATH in this file if PHP is installed elsewhere
    echo.
    pause
    exit /b 1
)

:: Remove existing link/directory if exists
if exist "public\storage" (
    echo Removing existing public\storage...
    rmdir /s /q "public\storage" 2>nul
    if exist "public\storage" (
        echo Warning: Could not remove existing public\storage
        echo You may need to delete it manually
        echo.
    )
)

:: Method 1: Try artisan storage:link
echo Method 1: Trying php artisan storage:link...
"%PHP_PATH%" artisan storage:link
if %errorLevel% equ 0 (
    if exist "public\storage" (
        goto :success
    )
)

echo.
echo Method 1 completed, checking result...

:: Check if link was created
if exist "public\storage" (
    goto :success
)

:: Method 2: Try using mklink directly
echo.
echo Method 2: Trying mklink command...
set "TARGET=%~dp0storage\app\public"
set "LINK=%~dp0public\storage"

:: Convert to short paths to avoid spaces issues
for %%I in ("%TARGET%") do set "TARGET_SHORT=%%~sI"
for %%I in ("%LINK%") do set "LINK_SHORT=%%~sI"

mklink /D "%LINK_SHORT%" "%TARGET_SHORT%" >nul 2>&1
if %errorLevel% equ 0 (
    if exist "public\storage" (
        goto :success
    )
)

:: Method 3: Try PHP script
echo.
echo Method 2 failed. Trying Method 3: PHP script...
"%PHP_PATH%" create_storage_link.php
if exist "public\storage" (
    goto :success
)

:: All methods failed
echo.
echo ========================================
echo   ERROR: Could not create storage link
echo ========================================
echo.
echo All methods failed. Please try running manually:
echo.
echo   1. Open Command Prompt as Administrator
echo   2. Navigate to: %CD%
echo   3. Run: "%PHP_PATH%" artisan storage:link
echo.
echo Or try:
echo   mklink /D "public\storage" "storage\app\public"
echo.
pause
exit /b 1

:success
echo.
echo ========================================
echo   SUCCESS! Storage link created!
echo ========================================
echo.
echo The symbolic link from public\storage to storage\app\public has been created.
echo.
echo All files in storage/app/public are now accessible via:
echo   http://localhost/library/public/storage/
echo.
echo Images and audio files should now appear on your website!
echo.
pause
exit /b 0

