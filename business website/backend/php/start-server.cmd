@echo off
REM Start PHP built-in server serving the public folder on port 8080
cd /d %~dp0\public
php -S localhost:8080
pause
