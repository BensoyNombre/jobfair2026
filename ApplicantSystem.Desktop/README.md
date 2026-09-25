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
3. On Desktop B, run `DesktopB-OpenApplicantSystem.bat` and enter Desktop A's IPv4 address, for example `192.168.1.46`.
4. Keep Desktop A powered on and connected to the same network while Desktop B is using the system.

The shared address is `http://<Desktop-A-IP>/applicant_system/index.php`. If Desktop B cannot connect, allow Apache through Windows Firewall on Desktop A and confirm that both computers are on the same network.
