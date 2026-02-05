<#
Monitors `storage/logs/laravel.log` for recent errors and saves an alert snapshot when found.
Runs non-interactively; intended to be scheduled (every 5 minutes).
#>

Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass -Force

$root = Split-Path -Parent $MyInvocation.MyCommand.Path | Resolve-Path | ForEach-Object { $_.ProviderPath }
$log = Join-Path $root 'storage\logs\laravel.log'
$alertsDir = Join-Path $root 'storage\logs\monitor-alerts'
if (-not (Test-Path $alertsDir)) { New-Item -ItemType Directory -Path $alertsDir | Out-Null }

if (-not (Test-Path $log)) { Exit 0 }

# get last 300 lines
$lines = Get-Content -Path $log -Tail 300 -ErrorAction SilentlyContinue
if (-not $lines) { Exit 0 }

$text = $lines -join "`n"

# look for error indicators
$regex = '(?i)\b(error|exception|critical|failed|SQLSTATE)\b'
if ($text -notmatch $regex) { Exit 0 }

# create alert snapshot
$timestamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$alertFile = Join-Path $alertsDir "alert_$timestamp.log"

"=== Alert captured at $(Get-Date) ===" | Out-File -FilePath $alertFile -Encoding utf8
"--- Last 300 lines of laravel.log ---" | Out-File -FilePath $alertFile -Append -Encoding utf8
$text | Out-File -FilePath $alertFile -Append -Encoding utf8

"--- PM2 process list ---" | Out-File -FilePath $alertFile -Append -Encoding utf8
try { pm2 ls | Out-File -FilePath $alertFile -Append -Encoding utf8 } catch { "pm2 not available" | Out-File -FilePath $alertFile -Append -Encoding utf8 }

"--- Listening ports (netstat) ---" | Out-File -FilePath $alertFile -Append -Encoding utf8
try { netstat -ano | findstr ":8000" | Out-File -FilePath $alertFile -Append -Encoding utf8 } catch { }

"Alert saved: $alertFile" | Out-File -FilePath (Join-Path $alertsDir 'monitoring.log') -Append -Encoding utf8
