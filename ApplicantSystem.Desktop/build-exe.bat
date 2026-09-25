@echo off
setlocal
cd /d "%~dp0"

dotnet publish ApplicantSystem.Desktop.csproj -c Release -r win-x64 --self-contained true -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true -o ..\dist
if errorlevel 1 (
    echo.
    echo Build failed.
    pause
    exit /b 1
)

echo.
echo Executable created at: ..\dist\ApplicantSystem.exe
pause
