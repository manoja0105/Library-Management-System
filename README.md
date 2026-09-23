Library Management System
1. Project Title
Library Management System
2. Project Description
The Library Management System is a web-based application developed to help a librarian manage library books, students, borrowing and returning activities, and reports.
The system provides a simple and organized way to maintain library records and reduce manual work. The librarian can manage books and student information, issue books, record returned books, search available books, and generate reports.
3. Problem Statement
Traditional library management methods often depend on manual records, which can make it difficult to maintain accurate information about books, students, and borrowing activities.
Manual record keeping can also result in:
•	Difficulty finding book information
•	Difficulty tracking borrowed and returned books
•	Errors in maintaining student records
•	Time-consuming record management
•	Difficulty preparing library reports
•	Problems identifying available books
Therefore, a computerized Library Management System is required to manage these activities efficiently.
4. Project Objectives
The main objectives of the project are:
•	To develop a simple web-based Library Management System.
•	To maintain accurate book records.
•	To maintain student/member records.
•	To manage book issuing and returning.
•	To display currently available books.
•	To provide search functionality.
•	To generate useful library reports.
•	To reduce manual record-keeping work.
•	To provide secure librarian access to administrative functions.
5. Main Features
Librarian Login
•	librarian login
•	Logout functionality
Dashboard
•	Total books
•	Total students
•	Available books
•	Issued books
Book Management
•	Add books
•	View books
•	Search books
•	Edit book information
•	Delete books 
•	Track total and available quantities
Student Management
•	Add students
•	View students
•	Search student records
•	Edit student information
•	Delete student records
Borrowing Management
•	Issue books to students
•	Set issue and due dates
•	Record returned books
•	Automatically update available quantity
•	Display borrowing status
Reports
•	Monthly borrowing report
•	Student borrowing report
•	Book borrowing report
Public Book Search
Users can search available books by:
•	Book ID
•	Title
•	Author
Only books with available copies are displayed.
6. Technologies and Tools Used
Frontend
•	HTML5
•	CSS
•	JavaScript
Backend
•	PHP
Database
•	MySQL
Development Environment
•	XAMPP
•	Apache
•	MySQL
Version Control
•	Git
•	GitHub
Development Tools
•	Visual Studio Code
•	phpMyAdmin
•	Web Browser
7. Project Scope
Included in the Project
The system includes:
•	Librarian authentication
•	Book management
•	Student management
•	Book issue management
•	Book return management
•	Available book search
•	Borrowing records
•	Monthly reports
•	Student reports
•	Book reports
•	MySQL database management
Not Included in the Current Version
The following features are outside the scope of Version 1.0:
•	Student login
•	Online book reservation
•	Book renewal
•	Online fine payment
•	Email/SMS notifications
•	Barcode scanning
•	E-book management
•	Mobile application
These features may be considered for future development.
8. System Users
Librarian
The librarian is the main system user and can:
•	Log in to the system
•	Manage books
•	Manage students
•	Issue books
•	Return books
•	View borrowing records
•	Generate reports
Student
Students do not require a login in Version 1.0. They can search and view available books through the public book-search interface.
9. Database
The project uses a MySQL database named:
library_management
Main tables include:
•	users
•	students
•	books
•	borrowings
The database SQL file is available in:
schema.sql
seed.sql
10. Installation and Setup
Step 1: Install XAMPP
Install XAMPP with:
•	Apache
•	MySQL
•	PHP
Step 2: Copy the Project
Copy the project folder into the XAMPP htdocs directory.
Example:
C:\xampp\htdocs\Library Management System
Step 3: Start XAMPP
Start:
•	Apache
•	MySQL
Step 4: Create the Database
Open phpMyAdmin and import:
schema.sql
seed.sql
This will create the required database and tables.
Step 5: Configure Database Connection
Check:
config/db.php
Make sure the database connection details match your local XAMPP configuration.
Step 6: Run the System
Open the project in a web browser.
Example:
http://localhost/Library Management System/
11. Project Structure
Library Management System/
│
├── config/
├── includes/
├── public/
├── assets/
├── schema.sql
├── documentation/
├── README.md
└── .gitignore
12. Student Details
Student Name: T. Manoja
Course: Software Change Management
Project: Library Management System
Technology: PHP, MySQL, HTML, CSS, JavaScript
Development Environment: XAMPP
13. Documentation
Project-related documentation is available in the documents folder.
The documentation may include:
•	Project Proposal
•	Software Requirements Documentation
14. Version
Version: 1.0
Project Status: Final
15.
username: admin
password:123456