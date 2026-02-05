@echo off
cd /d "%~dp0.."
start /b "" powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File "%~dp0pm2_resurrect.ps1" >nul 2>&1
exit /b 0
