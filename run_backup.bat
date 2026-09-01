@echo off
title SIMAKS Automated Database Backup
echo ========================================================
echo        SIMAKS AUTOMATED DATABASE BACKUP RUNNER
echo ========================================================
echo.
php "%~dp0scripts\auto_backup.php"
echo.
pause
