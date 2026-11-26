# Attendance Management System

A web-based attendance system built with **Laravel 8**, **MySQL**, and **Bootstrap 5** for BSIS 4B Application Development.

Teachers can keep a list of students, take attendance for a whole section in one screen, edit or delete records, and print or export an attendance report.

## Features

| Module | What it does |
| --- | --- |
| **Login** | Only signed-in users can open the system. Login attempts are limited to 5 per minute. Deactivated accounts cannot sign in. |
| **Manage Users** (admin, CRUD) | The admin adds, edits, deactivates/activates and deletes user accounts, and can reset a password. You cannot deactivate or delete yourself, and there is always at least one active admin. |
| **Change Password** | Every user changes their own password (the current password is asked first; new one needs 8+ characters with letters and numbers). |
| **Dashboard** | Today's count of Present, Late, Absent and Excused, students not yet recorded, recent records, and the students with the most absences. |
| **Students** (CRUD) | Add, view, edit and delete students. Search by student no. or name, filter by section. Each student has a profile with their attendance history. |
| **Take Attendance** | Pick a date and section, then mark every student at once (Mark all Present / Absent). Time In is filled in automatically for Present and Late. Saving again updates the same records instead of making duplicates. |
| **Attendance Records** (CRUD) | Add, edit and delete single records. Filter by date range, status, section and student. |
| **Reports** | Attendance summary per student for any date range and section, with attendance rate. Print it or export it to CSV (opens in Excel). |
| **Posts** (CRUD) | The original Posts CRUD from the first activity. |

### Rules the system enforces

- A student number can only be used once.
- A student can only have one attendance record per day.
- Attendance cannot be recorded for a future date.
- Time In is kept only for Present and Late.
- Deleting a student also deletes that student's attendance records (the system asks first).

## Database

| Table | Columns |
| --- | --- |
| `students` | id, student_no (unique), first_name, last_name, course, year_level, section |
| `attendances` | id, student_id → students, date, status (present / late / absent / excused), time_in, remarks; unique (student_id, date) |
| `users` | the accounts that can log in |
| `posts` | id, title, content |

## How to run (XAMPP)

1. Start **MySQL** in the XAMPP Control Panel.
2. Create an empty database named `bsis4b_plaza` (phpMyAdmin or `CREATE DATABASE bsis4b_plaza;`).
3. In this folder, run:

   ```
   composer install
   copy .env.example .env
   php artisan key:generate
   ```

4. Check `.env` (the example already has these):

   ```
   APP_NAME="Attendance Management"
   DB_DATABASE=bsis4b_plaza
   ```

5. Create the tables and the sample data:

   ```
   php artisan migrate --seed
   ```

6. Start the app and open http://localhost:8000

   ```
   php artisan serve
   ```

### Demo accounts

| Role | Email | Password |
| --- | --- | --- |
| Admin (can Manage Users) | admin@attendance.test | admin123 |
| Teacher (normal user) | teacher@attendance.test | teacher2026 |

The sample data has 12 students (sections 4A and 4B) and attendance for the last 5 school days. The names are made up. To start over with fresh sample data, run `php artisan migrate:fresh --seed`.

> Change the demo password before using the system with real students.

## Project structure

```
app/Http/Controllers/
    AuthController.php        login and logout
    DashboardController.php   dashboard numbers
    StudentController.php     students CRUD
    AttendanceController.php  records CRUD and Take Attendance
    ReportController.php      report and CSV export
    PostController.php        posts CRUD
app/Models/                   Student, Attendance, Post, User
database/migrations/          table definitions
database/seeders/             demo account and sample data
resources/views/              Blade pages (layouts, students, attendances, reports, posts, auth)
routes/web.php                all routes (everything except login needs a signed-in user)
```
