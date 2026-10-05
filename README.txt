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

composer --version

composer require phpmailer/phpmailer

Make sure vendor/autoload.php exists.


5. Open Windows Task Scheduler

First Click Tast Scheduler Library then in Action Create Task Scheduler — "Notification"

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
C:\xampp\php\php.exe
Add arguments:
"C:\xampp\htdocs\Library Management System\scripts\send_hold_notifications.php"
start in:
"C:\xampp\htdocs\Library Management System"

Conditions tab

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


Create another task:

Create Task Scheduler — Expiry



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
C:\xampp\php\php.exe
Add arguments:
"C:\xampp\htdocs\Library Management System\scripts\expire_holds.php"

start in:
"C:\xampp\htdocs\Library Management System"

Conditions tab

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

 Click ok



6.Test a 5-Minute Reminder / 10-Minute Expiry

After completing the Task Scheduler setup, perform a real test to verify that the hold reminder email and automatic expiry are working correctly.

Step 1 — Create a Hold

Open the public books page.

Follow these steps:

Public Books
      ↓
Select an available book
      ↓
Click Hold Book
      ↓
Enter student details
      ↓
Create Hold

For example, suppose the hold is created at:

10:00 AM

Check the book_holds table in the database.

The record should show values similar to:

status = ACTIVE
notification_sent = 0
hold_time = 10:00 AM
expiry_time = 10:10 AM

The book's available_quantity should also be reduced when the hold is successfully created.

Step 2 — Check the Reminder Email

The notification Task Scheduler runs every 1 minute and checks the database for eligible holds.

For a 5-minute test, the reminder should be sent at approximately:

10:05 AM

The student should receive the reminder email through PHPMailer and SMTP.

After the email is successfully sent, check the book_holds table.

The value should change to:

notification_sent = 1

The hold should still have:

status = ACTIVE

Therefore:

10:00 AM → Hold Created
10:05 AM → Reminder Email Sent
Step 3 — Check Automatic Expiry

At approximately:

10:10 AM

the expiry Task Scheduler should process the hold.

The hold status should change:

ACTIVE → EXPIRED

The system should also restore the book's available quantity.

Therefore:

10:10 AM → Hold Expired

The expired hold should not be deleted from the database.

Step 4 — Check the Librarian Hold Page

Open the librarian's Hold Management page.

The tested hold should remain visible in the hold list.

Its status should be:

EXPIRED

When the hold is expired, the following actions should no longer be available:

Borrow
Cancel

The final result should look conceptually like:

Student     Book              Status
----------------------------------------
Manoja      The Alchemist     EXPIRED

There should be no Borrow or Cancel button for the expired hold.


7.username: admin
  password:123456