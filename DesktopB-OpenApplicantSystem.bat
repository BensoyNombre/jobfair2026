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
set "APP_EXE=%~dp0dist\ApplicantSystem.exe"

if not exist "%APP_EXE%" (
    echo.
    echo ApplicantSystem.exe was not found in the dist folder.
    echo Copy the published dist folder beside this launcher.
    pause
    exit /b 1
)

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

start "Applicant System" "%APP_EXE%" "%APP_URL%"
endlocal
