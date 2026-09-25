@echo off
setlocal EnableExtensions

set "APP_PATH=/applicant_system/index.php"
set "CONFIG_FILE=%~dp0desktop-a-host.txt"
set "SERVER_HOST="

if exist "%CONFIG_FILE%" set /p "SERVER_HOST="<"%CONFIG_FILE%"

if not defined SERVER_HOST (
    echo Enter Desktop A's IPv4 address or computer name.
    echo Example: 192.168.1.25
    set /p "SERVER_HOST=Desktop A: "
    if not defined SERVER_HOST (
        echo No server address was entered.
        pause
        exit /b 1
    )
    >"%CONFIG_FILE%" echo %SERVER_HOST%
)

set "APP_URL=http://%SERVER_HOST%%APP_PATH%"

echo Checking %APP_URL% ...
curl.exe --silent --head --fail --max-time 5 "%APP_URL%" >nul 2>&1
if errorlevel 1 (
    echo.
    echo Cannot reach Desktop A at %SERVER_HOST%.
    echo Check that Desktop A is on, Apache is running, and the address is correct.
    echo Delete "%CONFIG_FILE%" to enter a different address next time.
    pause
    exit /b 1
)

set "EDGE_EXE=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
if not exist "%EDGE_EXE%" set "EDGE_EXE=%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"

if exist "%EDGE_EXE%" (
    start "Applicant System" "%EDGE_EXE%" --app="%APP_URL%" --start-maximized
) else (
    start "Applicant System" "%APP_URL%"
)
endlocal
