@echo off

set "SCRIPT_DIR=%~dp0"

"%SCRIPT_DIR%..\..\php\php.exe" "%SCRIPT_DIR%expire_holds.php" >> "%SCRIPT_DIR%hold_expiry.log" 2>&1

exit /b 0
