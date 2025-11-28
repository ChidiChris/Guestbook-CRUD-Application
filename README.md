Guestbook CRUD Application (Single-File PHP Project)

A secure, database-driven Guestbook application built as part of the Bincom PHP/MySQL Beginners Class Test.
The entire project runs inside one PHP file (guestbook.php) and demonstrates:

✔ Create
✔ Read
✔ Update
✔ Delete
✔ Security (CSRF + Prepared Statements)
✔ Clean UI with external CSS

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

Database:

CREATE DATABASE guestbook_db;


Table:

CREATE TABLE entries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(255) NOT NULL,
    message_text TEXT NOT NULL,
    submission_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

📝 Original Test Question (Fully Included in README)

Below is the exact requirement from the Bincom test, added for clarity and assessment:

📄 Test Question / Assignment Requirements

Goal:
Create a functional, database-driven web application in a single PHP file demonstrating Full CRUD.

Part 1: Database Setup

Create guestbook_db

Create table entries with:

id (INT, PK, Auto Increment)

guest_name (VARCHAR)

message_text (TEXT)

submission_time (DATETIME, default = CURRENT_TIMESTAMP)

⚙️ How the Application Works
1. READ Operation

Establishes a secure MySQLi connection

Fetches all entries with:

SELECT * FROM entries ORDER BY submission_time DESC;


Displays:

Guest name

Message

Timestamp

Edit button

Delete button

2. CREATE Operation (POST)

Users submit:

Name

Message

Security includes:

Required field validation

Length checks

CSRF token validation

Prepared INSERT:

INSERT INTO entries (guest_name, message_text) VALUES (?, ?);


Redirect using PRG pattern:

header("Location: guestbook.php");

3. UPDATE / EDIT Operation (POST)

When user clicks “Edit”:

Entry is loaded using ID

A pre-filled form appears

User updates name or message

Security checks:

ID must be numeric

Entry must exist

CSRF token must match

Fields validated & length checked

UPDATE uses prepared statement:

UPDATE entries SET guest_name = ?, message_text = ? WHERE id = ? LIMIT 1;


After update, redirect:

header("Location: guestbook.php");
exit;

4. DELETE Operation (GET + CSRF)

Delete link includes:

guestbook.php?delete=ID&token=CSRF_TOKEN


Process:

ID validated

CSRF token validated

Entry existence confirmed

Secure deletion with:

DELETE FROM entries WHERE id = ? LIMIT 1;


Redirect to clear URL parameters

🔐 Security Features
Security Risk	Protection
SQL Injection	Prepared statements
XSS	htmlspecialchars()
CSRF	Token stored in sessions
URL Tampering	Strict numeric ID check
Accidental Double Submit	Redirect after POST

🎨 CSS Styling (style.css)

Includes:

Clean card layout

Form styling

Edit form + main form alignment

Buttons (Save, Cancel, Update, Delete)

Error messages

Mobile-friendly responsive layout

🧪 Submission Checklist (Updated: Full CRUD)
✔ DATABASE

 guestbook_db created

 entries table created correctly

✔ READ

 Connection successful

 Entries displayed in correct order

✔ CREATE

 POST form working

 Validates & sanitizes input

 Inserts using prepared statements

 Redirect after POST

✔ UPDATE (NEW)

 Edit button shows a pre-filled form

 ID validated & entry fetched safely

 CSRF validated

 Update uses prepared statements

 Redirect after update

✔ DELETE

 Delete link passes ID

 CSRF token validated

 Prepared DELETE with WHERE clause

 Redirect after delete

✔ STRUCTURE

 All logic in a single PHP file

 Output safely escaped

📝 Original Test Requirements (With UPDATE Added)

(The original test required only CRD, but this project extends it to true CRUD.)

We now support:

Create

Read

Update

Delete

The project still fulfills every test requirement plus additional functionality.

🚀 How to Run Locally

Install XAMPP/WAMP

Start Apache + MySQL

Create DB + Table

Place project inside:

htdocs/guestbook/


Run from browser:

http://localhost/guestbook/guestbook.php

🔮 Future Improvements

Pagination

Rich text messages

Search/filter messages

User login system

AJAX without reload

Export to CSV

👤 Author

Chidiebube Christopher Onwugbufor
Guestbook CRUD Application — PHP/MySQL Beginners Test
Date: November 2025