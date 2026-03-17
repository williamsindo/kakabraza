# Import the MySQL schema for dumsel_merchants
# Prompts for DB user and password and runs the init.sql migration

$mysql = Get-Command mysql -ErrorAction SilentlyContinue
if (-not $mysql) {
    Write-Host "mysql client not found in PATH. Install MySQL or add mysql.exe to PATH." -ForegroundColor Red
    exit 1
}

$defaultUser = 'root'
$user = Read-Host "MySQL user (default: root)"
if ([string]::IsNullOrEmpty($user)) { $user = $defaultUser }
$pass = Read-Host "MySQL password (leave empty for no password)"

$scriptPath = Join-Path $PSScriptRoot 'migrations\init.sql'
if (-not (Test-Path $scriptPath)) {
    Write-Host "Migration file not found at: $scriptPath" -ForegroundColor Red
    exit 1
}

Write-Host "Importing database using user '$user'..."

if ([string]::IsNullOrEmpty($pass)) {
    & mysql -u $user < $scriptPath
} else {
    & mysql -u $user -p$pass < $scriptPath
}

if ($LASTEXITCODE -eq 0) {
    Write-Host "Database imported successfully." -ForegroundColor Green
} else {
    Write-Host "There was an error importing the database (exit code $LASTEXITCODE)." -ForegroundColor Red
}
