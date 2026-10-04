Setup order
1. Install XAMPP

Install XAMPP.
Apache + MySQL available.

2. Copy the project

C:\XAMP\htdocs\Library Management System

3. Import the database

Open phpMyAdmin.
Import schema.sql.

4. Install Composer / PHPMailer

composer require phpmailer/phpmailer

Make sure vendor/autoload.php exists.


5. Open Windows Task Scheduler

Create Task Scheduler — Notification

Open Task Scheduler → Create Task

General

Name: Library Hold Notification
Select Run whether user is logged on or not
Select Run with highest privileges

Triggers → New

Begin the task: On a schedule
Daily: ✓
Repeat task every: 1 minute
For a duration: Indefinitely
Enabled: ✓

Actions → New

Action: Start a program
Program/script:
C:\XAMP\php\php.exe
Add arguments:
"C:\XAMP\htdocs\Library Management System\scripts\send_hold_notifications.php"

Conditions

Important: Leave all of them unchecked.

Settings tab

Use:

Settings

☑ Allow task to be run on demand

☑ Run task as soon as possible after a scheduled start is missed

☑ If the task fails, restart every:
   1 minute
   Attempt to restart up to: 3 times

☑ If the running task does not end when requested,
   force it to stop

☑ If the task is already running:
   Do not start a new instance


Create Task Scheduler — Expiry

Create another task:

General

Name: Library Hold Expiry
Select Run whether user is logged on or not
Select Run with highest privileges

Triggers → New

Begin the task: On a schedule
Daily: ✓
Repeat task every: 1 minute
For a duration: Indefinitely
Enabled: ✓

Actions → New

Action: Start a program
Program/script:
C:\XAMP\php\php.exe
Add arguments:
"C:\XAMP\htdocs\Library Management System\scripts\expire_holds.php"

Conditions

Important: Leave all of them unchecked.

Settings tab

Use:

Settings

☑ Allow task to be run on demand

☑ Run task as soon as possible after a scheduled start is missed

☑ If the task fails, restart every:
   1 minute
   Attempt to restart up to: 3 times

☑ If the running task does not end when requested,
   force it to stop

☑ If the task is already running:
   Do not start a new instance





6. Test PHPMailer
"C:\XAMP\php\php.exe" "scripts\test_email.php"

Create a hold.
Confirm the email system works.




7.username: admin
  password:123456