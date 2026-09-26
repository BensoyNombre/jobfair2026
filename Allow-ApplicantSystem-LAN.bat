@echo off
setlocal

net session >nul 2>&1
if errorlevel 1 (
    echo Right-click this file and choose Run as administrator.
    pause
    exit /b 1
)

netsh advfirewall firewall delete rule name="Applicant System Apache HTTP (Private)" >nul 2>&1
netsh advfirewall firewall add rule name="Applicant System Apache HTTP (Private)" dir=in action=allow protocol=TCP localport=80 profile=private
if errorlevel 1 (
    echo Could not create the firewall rule.
    pause
    exit /b 1
)

echo Apache HTTP access is allowed on Private networks only.
pause