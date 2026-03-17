-- Database Schema for Hospital Management System

SET FOREIGN_KEY_CHECKS=0;

-- Users Table (All roles)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'doctor', 'nurse', 'receptionist', 'patient') NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Patients Table
DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL, -- Optional link to user login
    `name` VARCHAR(100) NOT NULL,
    `dob` DATE NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
    `contact` VARCHAR(20),
    `address` TEXT,
    `medical_history` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- Doctors Table
DROP TABLE IF EXISTS `doctors`;
CREATE TABLE `doctors` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `specialization` VARCHAR(100) NOT NULL,
    `availability` TEXT, -- Simplified text for schedule description
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- Appointments Table
DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `doctor_id` INT NOT NULL,
    `appointment_date` DATETIME NOT NULL,
    `reason` TEXT,
    `status` ENUM('Booked', 'Completed', 'Cancelled') DEFAULT 'Booked',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE
);

-- Medicines Table (Pharmacy)
DROP TABLE IF EXISTS `medicines`;
CREATE TABLE `medicines` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `stock_quantity` INT NOT NULL DEFAULT 0
);

-- Medical Records
DROP TABLE IF EXISTS `medical_records`;
CREATE TABLE `medical_records` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `doctor_id` INT NOT NULL,
    `diagnosis` TEXT,
    `prescription` TEXT, -- Could be JSON or text reference to medicines
    `lab_results_path` VARCHAR(255) NULL, -- Path to uploaded file
    `visit_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`doctor_id`) REFERENCES `doctors`(`id`) ON DELETE CASCADE
);

-- Bills / Payments
DROP TABLE IF EXISTS `bills`;
CREATE TABLE `bills` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('Unpaid', 'Paid') DEFAULT 'Unpaid',
    `generated_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE
);

-- Beds (Ward Management)
DROP TABLE IF EXISTS `beds`;
CREATE TABLE `beds` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ward_number` VARCHAR(20) NOT NULL,
    `bed_number` VARCHAR(20) NOT NULL,
    `is_occupied` BOOLEAN DEFAULT FALSE
);

-- Admissions
DROP TABLE IF EXISTS `admissions`;
CREATE TABLE `admissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `bed_id` INT NOT NULL,
    `admission_date` DATETIME NOT NULL,
    `discharge_date` DATETIME NULL,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`bed_id`) REFERENCES `beds`(`id`) ON DELETE CASCADE
);

-- Lab Tests
DROP TABLE IF EXISTS `lab_tests`;
CREATE TABLE `lab_tests` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `patient_id` INT NOT NULL,
    `test_type` VARCHAR(100) NOT NULL,
    `result` TEXT,
    `test_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`patient_id`) REFERENCES `patients`(`id`) ON DELETE CASCADE
);

SET FOREIGN_KEY_CHECKS=1;
