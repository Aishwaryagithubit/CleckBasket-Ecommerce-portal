@echo off
title CleckBasket RFID Bridge
color 0A
echo.
echo  =========================================
echo   CleckBasket RFID Bridge  - Starting...
echo  =========================================
echo.

cd /d "%~dp0"
py serial_bridge.py

echo.
echo  Bridge exited.
pause
