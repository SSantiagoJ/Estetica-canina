@echo off
title Pet Grooming - Laravel Server
cd /d "%~dp0"
echo.
echo ==========================================
echo   Pet Grooming - Servidor Laravel
echo ==========================================
echo.
echo Carpeta: %cd%
echo URL: http://127.0.0.1:8000
echo.
echo IMPORTANTE: deja esta ventana abierta mientras uses el sistema.
echo Para detener el servidor, presiona CTRL + C.
echo.
"C:\xampp\php\php.exe" artisan optimize:clear
"C:\xampp\php\php.exe" artisan serve --host=127.0.0.1 --port=8000
echo.
echo El servidor se detuvo. Revisa el mensaje anterior si hubo un error.
pause
