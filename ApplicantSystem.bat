@echo off
set "APP_URL=http://localhost/applicant_system/index.php"
set "LAN_IP="

for /f "usebackq delims=" %%I in (`powershell.exe -NoProfile -Command "Get-NetIPConfiguration | Where-Object { $_.IPv4DefaultGateway -and $_.IPv4Address } | Select-Object -First 1 -ExpandProperty IPv4Address | Select-Object -ExpandProperty IPAddress"`) do set "LAN_IP=%%I"

curl.exe --silent --head --fail "%APP_URL%" >nul 2>&1
if errorlevel 1 (
    echo Apache is not running. Start Apache in the XAMPP Control Panel, then try again.
    pause
    exit /b 1
)

echo.
echo Desktop A URL: %APP_URL%
if defined LAN_IP echo Desktop B URL: http://%LAN_IP%/applicant_system/index.php
echo.

set "EDGE_EXE=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
if not exist "%EDGE_EXE%" set "EDGE_EXE=%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"

if exist "%EDGE_EXE%" (
    start "Applicant System" "%EDGE_EXE%" --app="%APP_URL%" --start-maximized
) else (
    start "Applicant System" "%APP_URL%"
)
