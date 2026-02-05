$logFile = (Join-Path $PSScriptRoot '..' | Join-Path -ChildPath 'storage\logs\pm2_resurrect.log')
$null = New-Item -ItemType Directory -Force -Path (Split-Path $logFile -Parent) 2>&1

# Suppress all errors and output
$ErrorActionPreference = 'SilentlyContinue'
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass -Force -ErrorAction SilentlyContinue 2>&1 | Out-Null
cd (Join-Path $PSScriptRoot '..') -ErrorAction SilentlyContinue 2>&1 | Out-Null
pm2 resurrect 2>&1 | Out-Null
pm2 ls 2>&1 | Out-Null

"[$(Get-Date)] PM2 Resurrect completed" | Out-File -FilePath $logFile -Append -Encoding utf8 -ErrorAction SilentlyContinue 2>&1 | Out-Null
