@echo off

"C:\XAMP\php\php.exe" "C:\XAMP\htdocs\Library Management System\scripts\expire_holds.php" >> "C:\XAMP\htdocs\Library Management System\scripts\hold_expiry.log" 2>&1

exit /b 0