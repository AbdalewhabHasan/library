@echo off
REM Simple storage link creator - requires Administrator privileges
REM For a more comprehensive solution, use fix_storage.bat instead

echo ========================================
echo   Simple Storage Link Creator
echo ========================================
echo.
echo This script requires Administrator privileges.
echo If prompted, click Yes to allow.
echo.
pause

cd /d "%~dp0"
set "LINK=%CD%\public\storage"
set "TARGET=%CD%\storage\app\public"

if not exist "%TARGET%" (
    echo Error: Target directory does not exist: %TARGET%
    pause
    exit /b 1
)

if exist "%LINK%" (
    echo Removing existing link/directory...
    rd /s /q "%LINK%" 2>nul
)

echo Creating symbolic link...
mklink /D "%LINK%" "%TARGET%"

if exist "%LINK%" (
    echo.
    echo ========================================
    echo SUCCESS! Storage link created!
    echo ========================================
    echo All storage files are now accessible via public/storage
) else (
    echo.
    echo ERROR: Failed to create link.
    echo.
    echo Please try running as Administrator:
    echo   1. Right-click this file
    echo   2. Select "Run as Administrator"
    echo.
    echo Or use: fix_storage.bat (automatically requests admin)
)

pause

