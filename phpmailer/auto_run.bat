@echo off
:loop
php C:\xampp\htdocs\dropforlife\phpmailer\test.php
timeout /t 1 /nobreak >nul
goto loop
