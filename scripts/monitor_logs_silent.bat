@echo off
cd /d "%~dp0.."
start /b "" powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File "%~dp0monitor_logs.ps1" >nul 2>&1
exit /b 0
