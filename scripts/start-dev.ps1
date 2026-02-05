#!/usr/bin/env pwsh
# Helper to remove stale server.pid and start development processes.
try {
    $repoRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
} catch {
    $repoRoot = Get-Location
}

$serverPid = Join-Path $repoRoot 'server.pid'
if (Test-Path $serverPid) {
    try {
        $pidContent = Get-Content $serverPid -ErrorAction Stop | Select-Object -First 1
        if ($pidContent -match '^[0-9]+$') {
            Write-Host "Found stale PID: $pidContent — attempting to stop process..."
            Stop-Process -Id ([int]$pidContent) -ErrorAction SilentlyContinue
        }
    } catch {
        Write-Host "Could not read/stop PID from $serverPid — removing file anyway."
    }
    Remove-Item $serverPid -ErrorAction SilentlyContinue
}

Write-Host "Starting dev stack (composer run dev). Use Ctrl+C to stop."
composer run dev
