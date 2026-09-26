# Applicant System desktop executable

Kabalo ko wala sang may mabasa ni pero kung nabasahan mo ni tandaan mo nga biskan anomatabo kung gustohon mo kag kung amo na imo nga damgo wala kong pake gina usik ko lang oras mo HAHAHAHAHA

## Build

Run `build-exe.bat` from this folder. The published executable is written to `dist\ApplicantSystem.exe`.

## Runtime requirements

- Apache and MySQL must be running in XAMPP.
- Microsoft Edge WebView2 Runtime must be installed on the target computer. It is included with current Windows installations and Microsoft Edge.
- The PHP application must be available at `http://localhost/applicant_system/index.php`.

## Use Desktop A as the LAN server

1. On Desktop A, start Apache and MySQL in XAMPP.
2. Run `ApplicantSystem.bat` on Desktop A. It checks the server and displays Desktop A's LAN address.
3. Copy the published `dist` folder and `DesktopB-OpenApplicantSystem.bat` to Desktop B.
4. On Desktop B, run `DesktopB-OpenApplicantSystem.bat` and enter Desktop A's IPv4 address, for example `192.168.1.46`. The launcher starts the desktop executable and remembers the address on that device.
5. Keep Desktop A powered on and connected to the same network while other devices are using the system.

The shared address is `http://<Desktop-A-IP>/applicant_system/index.php`. On Desktop A, set the trusted Wi-Fi or Ethernet network profile to **Private**, then right-click `Allow-ApplicantSystem-LAN.bat` and choose **Run as administrator**. This adds a firewall rule for Apache on Private networks only. Windows devices can also run `ApplicantSystem.exe` directly and enter Desktop A's IPv4 address when prompted. Phones, tablets, and other non-Windows devices can open the shared address in a browser; they cannot run a Windows `.exe`.
