$ErrorActionPreference = 'Stop'
$mysqld = 'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqld.exe'
if (-not (Test-Path $mysqld)) {
  Write-Host "mysqld not found at $mysqld"; exit 1
}
try {
  Write-Host "Installing MySQL service using $mysqld"
  & $mysqld --install MySQL80
} catch {
  Write-Host "Install may have failed or service already exists: $_"
}
try {
  Set-Service -Name MySQL80 -StartupType Automatic -ErrorAction Stop
  Write-Host 'Set startup type to Automatic'
} catch {
  Write-Host "Could not set startup type: $_"
}
try {
  Start-Service -Name MySQL80 -ErrorAction Stop
  Write-Host 'MySQL80 started'
} catch {
  Write-Host "Could not start MySQL80 service: $_"
}
# Show status and last 50 log lines
Get-Service -Name MySQL80 | Format-List -Property Status,Name,DisplayName
$log = 'C:\laragon\data\mysql-8\mysqld.log'
if (Test-Path $log) { Get-Content -Path $log -Tail 50 | ForEach-Object { Write-Host $_ } } else { Write-Host 'mysqld.log not found' }
