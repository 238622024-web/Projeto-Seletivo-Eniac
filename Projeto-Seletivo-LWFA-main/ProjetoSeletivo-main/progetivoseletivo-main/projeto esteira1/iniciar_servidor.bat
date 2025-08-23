@echo off
echo Iniciando servidor PHP local para ENIAC LINK+...
echo.
echo Servidor sera iniciado em: http://localhost:8000
echo.
echo Para parar o servidor pressione Ctrl+C
echo.

cd /d "%~dp0"

php -S localhost:8000

pause
