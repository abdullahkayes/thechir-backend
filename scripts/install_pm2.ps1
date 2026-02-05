<#
Installs pm2 and starts the ecosystem defined in `scripts/ecosystem.config.js`.

Run in an elevated PowerShell session.

This script will:
- verify `node` and `npm` are available
- install `pm2` and `pm2-windows-service` globally
- start apps from the ecosystem file
- run `pm2 save` so processes persist across restarts

To install the pm2 Windows service wrapper you will need to run the
`pm2-service-install` (from `pm2-windows-service`) interactively as an
administrator after this script finishes.
#>

function Write-ErrAndExit($msg) {
    Write-Host $msg -ForegroundColor Red
    exit 1
}

if (-not (Get-Command node -ErrorAction SilentlyContinue)) {
    Write-ErrAndExit "Node.js is not installed or not in PATH. Install Node.js first: https://nodejs.org/"
}
if (-not (Get-Command npm -ErrorAction SilentlyContinue)) {
    Write-ErrAndExit "npm not found. Ensure Node.js/npm are installed."
}

Write-Host "Installing pm2 and pm2-windows-service globally (may prompt for elevation)..."
npm install -g pm2 pm2-windows-service

Write-Host "Starting apps from ecosystem..."
cd (Join-Path $PSScriptRoot '..')
pm2 start .\scripts\ecosystem.config.js
pm2 save

Write-Host "pm2 started apps and saved process list. To install the Windows service wrapper run (interactive, admin):"
Write-Host "  pm2-service-install"
Write-Host "Follow prompts to register pm2 as a Windows service that will resurrect processes on boot."

Write-Host "To view logs: pm2 logs"
Write-Host "To list processes: pm2 ls"
