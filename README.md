📝 Guestbook CRUD Application (Single-File PHP Project)

A secure, database-driven Guestbook application built as part of the Bincom PHP/MySQL Beginners Class Test.
The entire project runs inside one PHP file (guestbook.php) and demonstrates:

✅ Create

✅ Read

✅ Update

✅ Delete

✅ Security (CSRF + Prepared Statements)

✅ Clean UI with external CSS

📌 Project Features

This application implements full CRUD functionality:

Create new guestbook entries

Read and display all entries (latest first)

Update existing entries using a safe edit form

Delete entries with secure confirmation

Single-file PHP architecture

CSRF protection for Create, Update, and Delete

SQL Injection protection using prepared statements

XSS protection with htmlspecialchars()

Redirects to prevent duplicate submissions

📂 File Structure
guestbook/
│── guestbook.php      # Main single-file CRUD app (Create, Read, Update, Delete)
│── style.css          # Styling file
│── README.md          # Documentation

🛢️ Database Setup

Database Creation:

CREATE DATABASE guestbook_db;


Table Creation:

CREATE TABLE entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(255) NOT NULL,
    message_text TEXT NOT NULL,
    submission_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

📝 Original Test Question (Fully Included)

Goal:
Create a functional, database-driven web application in a single PHP file demonstrating Full CRUD.

Database Requirements:

Database: guestbook_db

Table: entries

id (INT, PK, Auto Increment)

guest_name (VARCHAR)

message_text (TEXT)

submission_time (DATETIME, default = CURRENT_TIMESTAMP)

⚙️ How the Application Works
1️⃣ READ Operation

Establishes a secure MySQLi connection

Fetches all entries:

SELECT * FROM entries ORDER BY submission_time DESC;


Displays:

Guest name

Message

Timestamp

Edit button

Delete button

2️⃣ CREATE Operation (POST)

Users submit: Name + Message

Security includes:

Required field validation

Length checks

CSRF token validation

Secure INSERT:

INSERT INTO entries (guest_name, message_text) VALUES (?, ?);


Redirects using PRG pattern (header("Location: guestbook.php"))

3️⃣ UPDATE / EDIT Operation (POST)

Click Edit to load entry by ID

Pre-filled form appears for updating

Security checks:

ID must be numeric

Entry must exist

CSRF token must match

Fields validated & length checked

Secure UPDATE:

UPDATE entries SET guest_name = ?, message_text = ? WHERE id = ? LIMIT 1;


Redirects after update:

header("Location: guestbook.php"); exit;

4️⃣ DELETE Operation (GET + CSRF)

Delete link includes:

guestbook.php?delete=ID&token=CSRF_TOKEN


Process:

ID validated

CSRF token validated

Entry existence confirmed

Secure deletion:

DELETE FROM entries WHERE id = ? LIMIT 1;


Redirects to clear URL parameters

🔐 Security Features
Security Risk	Protection
SQL Injection	Prepared statements
XSS	htmlspecialchars()
CSRF	Token stored in sessions
URL Tampering	Strict numeric ID check
Accidental Double Submit	Redirect after POST
🎨 CSS Styling (style.css)

Clean card layout for entries

Form styling

Edit form + main form alignment

Buttons: Save, Cancel, Update, Delete

Error messages

Mobile-friendly responsive layout

🧪 Submission Checklist (Full CRUD)

✅ DATABASE: guestbook_db created, entries table correct

✅ READ: Connection successful, entries displayed in correct order

✅ CREATE: POST form works, input validated & sanitized, prepared statements used, redirects after POST

✅ UPDATE: Edit button pre-fills form, ID validated, CSRF checked, prepared UPDATE, redirects

✅ DELETE: ID and CSRF validated, prepared DELETE, redirects

✅ STRUCTURE: All logic in a single PHP file, output safely escaped

🔮 Future Improvements

Pagination

Rich text messages

Search/filter messages

User login system

AJAX without reload

Export to CSV

🚀 How to Run Locally

Install XAMPP/WAMP

Start Apache + MySQL

Create database + table as above

Place project inside:

htdocs/guestbook/


Access in browser:

http://localhost/guestbook/guestbook.php

👤 Author

Chidiebube Christopher Onwugbufor
Guestbook CRUD Application — PHP/MySQL Beginners Test
Date: November 2025
