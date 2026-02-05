@echo off
REM Bulletproof scheduled task setup using wscript.exe (truly hidden execution)
setlocal enabledelayedexpansion
set SCRIPT_DIR=%~dp0

REM Delete old tasks
schtasks /Delete /TN "pm2-resurrect-thechir" /F >nul 2>&1
schtasks /Delete /TN "thechir-log-monitor" /F >nul 2>&1

REM Create tasks with wscript.exe (no console window)
schtasks /Create /SC ONLOGON /TN "pm2-resurrect-thechir" /TR "wscript.exe \"!SCRIPT_DIR!pm2_resurrect_hidden.vbs\"" /F
if errorlevel 1 (
    echo ERROR: Failed to create pm2-resurrect-thechir
    pause
    exit /b 1
)

schtasks /Create /SC MINUTE /MO 5 /TN "thechir-log-monitor" /TR "wscript.exe \"!SCRIPT_DIR!monitor_logs_hidden.vbs\"" /F
if errorlevel 1 (
    echo ERROR: Failed to create thechir-log-monitor
    pause
    exit /b 1
)

echo.
echo SUCCESS: Scheduled tasks created with wscript.exe (bulletproof hidden execution)
echo Task 1: pm2-resurrect-thechir (runs at logon)
echo Task 2: thechir-log-monitor (runs every 5 minutes)
echo.
echo No command windows will appear. If windows still show, restart Windows.
echo.
pause
