# Hospital Management System (HMS)

A complete Hospital Management System built with native PHP (PDO) and MySQL.

## Features
- **Role-Based Access**: Admin, Doctor, Nurse, Receptionist, Patient.
- **Modules**:
  - **User Management**: Add/Manage system users.
  - **Patient Management**: Register patients, view history.
  - **Appointments**: Book and manage doctor schedules.
  - **Medical Records**: Diagnoses, Prescriptions, Lab Results (File Upload).
  - **Pharmacy**: Inventory and dispensing.
  - **Laboratory**: Test requests and results.
  - **Wards**: Admission and Bed management.
  - **Billing**: Invoice generation and payment tracking.
  - **Reports**: System statistics and revenue.

## Setup Instructions (XAMPP/WAMP)

1.  **Database Setup**:
    - Open PHPMyAdmin (http://localhost/phpmyadmin).
    - Create a new database named `hms_db`.
    - Import the file `database/schema.sql`.
    - Import the file `database/seed.sql` to populate initial data.

2.  **Project Configuration**:
    - Copy the `hms_project` folder to your `htdocs` directory (e.g., `C:\xampp\htdocs\hms_project`).
    - Verify database credentials in `config/db.php`. Default is:
      - Host: `localhost`
      - DB: `hms_db`
      - User: `root`
      - Pass: (empty)

3.  **Run**:
    - Open your browser and go to: `http://localhost/hms_project/public/index.php`

## Default Login Credentials
All passwords are explicitly hashed in the seed file. For testing, you may need to reset them or create a new user via the provided SQL or PHP logic if the hash is not matching your environment.
The seed file uses a placeholder hash for `password123`.

**Admin**:
- Username: `admin`
- Password: `password123`

**Doctor**:
- Username: `doctor_smith`
- Password: `password123`

**Receptionist**:
- Username: `recep_mike`
- Password: `password123`

## Directory Structure
- `public/`: Web entry point (assets, index.php).
- `src/`: Backend PHP classes.
- `config/`: Database connection.
- `templates/`: HTML partials.
- `database/`: SQL dumps.
