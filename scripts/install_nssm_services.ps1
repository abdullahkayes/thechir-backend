<#
Installs NSSM (if missing), registers two Windows services to
supervise Laravel dev processes (artisan serve + queue:work).

Run this script as Administrator.
Usage: Open an elevated PowerShell in the project root and run:
    .\scripts\install_nssm_services.ps1

This script does NOT modify your database or application code.
#>

function Write-ErrAndExit($msg) {
    Write-Host $msg -ForegroundColor Red
    exit 1
}

$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltinRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "This script must be run as Administrator. Please re-run in an elevated PowerShell." -ForegroundColor Yellow
    exit 1
}

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
$root = Resolve-Path "$root\.." -ErrorAction SilentlyContinue | ForEach-Object { $_.ProviderPath }
if (-not $root) { $root = Get-Location }

$tools = Join-Path $root 'tools\nssm'
New-Item -ItemType Directory -Force -Path $tools | Out-Null

$nssmZip = Join-Path $tools 'nssm.zip'
$nssmExe = Join-Path $tools 'nssm.exe'

if (-not (Test-Path $nssmExe)) {
    Write-Host "Downloading nssm..."
    $url = 'https://nssm.cc/release/nssm-2.24.zip'
    try {
        Invoke-WebRequest -Uri $url -OutFile $nssmZip -UseBasicParsing -ErrorAction Stop
        Expand-Archive -Path $nssmZip -DestinationPath $tools -Force
        # pick win64 nssm.exe if present
        $candidate = Get-ChildItem -Path $tools -Recurse -Filter 'nssm.exe' | Where-Object { $_.FullName -match 'win64' } | Select-Object -First 1
        if (-not $candidate) { $candidate = Get-ChildItem -Path $tools -Recurse -Filter 'nssm.exe' | Select-Object -First 1 }
        if ($candidate) { Copy-Item $candidate.FullName $nssmExe -Force }
        Remove-Item $nssmZip -Force -ErrorAction SilentlyContinue
    } catch {
        Write-ErrAndExit "Failed to download/extract nssm: $_"
    }
}

if (-not (Test-Path $nssmExe)) { Write-ErrAndExit "nssm.exe not found in $tools" }

# find php
$phpCmd = (Get-Command php -ErrorAction SilentlyContinue).Source
if (-not $phpCmd) {
    Write-ErrAndExit "Could not find php executable in PATH. Ensure PHP is installed and in PATH (e.g., C:\\laragon\\bin\\php\\php-8.x\\)."
}

$serviceServe = 'thechir-artisan-serve'
$serviceQueue = 'thechir-queue-worker'

$artisan = Join-Path $root 'artisan'

function Install-Service($svcName, $appPath, $args, $workDir, $outLog) {
    & $nssmExe install $svcName $appPath $args
    & $nssmExe set $svcName AppDirectory $workDir
    & $nssmExe set $svcName AppStdout $outLog
    & $nssmExe set $svcName AppStderr $outLog
    & $nssmExe set $svcName AppRotateFiles 1
    & $nssmExe set $svcName AppRotateOnline 1
    & $nssmExe set $svcName AppEnvironmentExtra "PATH=$env:PATH"
    & $nssmExe start $svcName
}

$logsDir = Join-Path $root 'storage\logs\process-supervisor'
New-Item -ItemType Directory -Force -Path $logsDir | Out-Null

$serveLog = Join-Path $logsDir 'serve.log'
$queueLog = Join-Path $logsDir 'queue.log'

Write-Host "Installing service $serviceServe to run: $phpCmd artisan serve --host=127.0.0.1 --port=8000"
Install-Service $serviceServe $phpCmd "artisan serve --host=127.0.0.1 --port=8000" $root $serveLog

Write-Host "Installing service $serviceQueue to run: $phpCmd artisan queue:work --tries=1 --sleep=3 --memory=256 --timeout=60"
Install-Service $serviceQueue $phpCmd "artisan queue:work --tries=1 --sleep=3 --memory=256 --timeout=60" $root $queueLog

Write-Host "Services installed and started. Logs: $logsDir" -ForegroundColor Green
