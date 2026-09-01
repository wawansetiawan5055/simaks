@echo off
title SIMAKS Automated Database Backup Runner
echo ========================================================
echo        SIMAKS AUTOMATED DATABASE BACKUP RUNNER
echo ========================================================
echo.

IF EXIST "D:\BtSoft\php\80\php.exe" (
    "D:\BtSoft\php\80\php.exe" "%~dp0scripts\auto_backup.php"
) ELSE IF EXIST "D:\BtSoft\php\74\php.exe" (
    "D:\BtSoft\php\74\php.exe" "%~dp0scripts\auto_backup.php"
) ELSE (
    php "%~dp0scripts\auto_backup.php"
)

echo.
pause
