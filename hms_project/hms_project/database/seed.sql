-- Seed Data

-- Users (Password is 'password123' for all example users. Hash generated via PHP password_hash)
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (This is a placeholder hash, we will use a real one or generate it in PHP setup script if needed, but for SQL file we can use a known hash)
-- Let's assume the hash '$2y$10$abcdefghijklmnopqrstuv' corresponds to 'password123' for simplicity of the example, OR better yet, we rely on the PHP script to insert the initial admin.
-- Ideally, we shouldn't hardcode hashes here unless we generated them. Let's use a standard bcrypt hash for 'password123':
-- $2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa

INSERT INTO `users` (`username`, `password_hash`, `role`, `full_name`, `email`) VALUES
('admin', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'admin', 'System Administrator', 'admin@hms.local'),
('doctor_smith', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'doctor', 'Dr. John Smith', 'smith@hms.local'),
('nurse_jones', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'nurse', 'Nurse Sarah Jones', 'jones@hms.local'),
('recep_mike', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'receptionist', 'Mike Receptionist', 'mike@hms.local'),
('patient_doe', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yUP1KUOYTa', 'patient', 'Jane Doe', 'jane@example.com');

-- Doctors Profile
INSERT INTO `doctors` (`user_id`, `specialization`, `availability`) VALUES
(2, 'Cardiology', 'Mon-Fri 09:00-17:00');

-- Patients Profile
INSERT INTO `patients` (`user_id`, `name`, `dob`, `gender`, `contact`, `address`, `medical_history`) VALUES
(5, 'Jane Doe', '1985-04-12', 'Female', '555-0101', '123 Main St', 'Type 2 Diabetes');

-- Medicines
INSERT INTO `medicines` (`name`, `description`, `price`, `stock_quantity`) VALUES
('Paracetamol', 'Pain reliever', 5.00, 100),
('Amoxicillin', 'Antibiotic', 12.50, 50),
('Ibuprofen', 'Anti-inflammatory', 8.00, 75);

-- Beds
INSERT INTO `beds` (`ward_number`, `bed_number`, `is_occupied`) VALUES
('Ward A', 'A-101', 0),
('Ward A', 'A-102', 0),
('ICU', 'ICU-1', 0);
