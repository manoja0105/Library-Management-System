@echo off
set "SCRIPT_DIR=%~dp0"

"%SCRIPT_DIR%..\..\php\php.exe" "%SCRIPT_DIR%send_hold_notifications.php" >> "%SCRIPT_DIR%hold_notifications.log" 2>&1
