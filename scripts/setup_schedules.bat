@echo off
setlocal enabledelayedexpansion
set SCRIPT_DIR=%~dp0
schtasks /Delete /TN "pm2-resurrect-thechir" /F >nul 2>&1
schtasks /Delete /TN "thechir-log-monitor" /F >nul 2>&1
schtasks /Create /SC ONLOGON /TN "pm2-resurrect-thechir" /TR "\"!SCRIPT_DIR!pm2_resurrect_silent.bat\"" /F
schtasks /Create /SC MINUTE /MO 5 /TN "thechir-log-monitor" /TR "\"!SCRIPT_DIR!monitor_logs_silent.bat\"" /F
echo Scheduled tasks updated with start /b silent execution
