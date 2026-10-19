-- NurtureDose Vaccination Management System database
-- Import this file in phpMyAdmin or run: mysql -u root < database/schema.sql
CREATE DATABASE IF NOT EXISTS vaccination_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vaccination_management;
CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL, role ENUM('admin','parent','hospital') NOT NULL DEFAULT 'parent',
 status ENUM('active','pending','suspended') NOT NULL DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS hospitals (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id INT UNSIGNED NULL UNIQUE, name VARCHAR(160) NOT NULL,
 address VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, phone VARCHAR(30) NULL, email VARCHAR(190) NULL,
 status ENUM('active','pending','inactive') NOT NULL DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_hospital_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS children (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, parent_id INT UNSIGNED NOT NULL, full_name VARCHAR(140) NOT NULL,
 date_of_birth DATE NOT NULL, gender ENUM('female','male','other') NOT NULL, guardian_phone VARCHAR(30) NULL,
 notes TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_child_parent FOREIGN KEY(parent_id) REFERENCES users(id) ON DELETE CASCADE, INDEX(parent_id)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS vaccines (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(140) NOT NULL, dose VARCHAR(80) NOT NULL DEFAULT 'Dose 1',
 recommended_age VARCHAR(100) NULL, description TEXT NULL, status ENUM('available','unavailable') NOT NULL DEFAULT 'available',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unique_vaccine_dose(name,dose)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS hospital_vaccines (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, hospital_id INT UNSIGNED NOT NULL, vaccine_id INT UNSIGNED NOT NULL,
 status ENUM('available','unavailable') NOT NULL DEFAULT 'available', updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY unique_hospital_vaccine(hospital_id,vaccine_id),
 CONSTRAINT fk_hv_hospital FOREIGN KEY(hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
 CONSTRAINT fk_hv_vaccine FOREIGN KEY(vaccine_id) REFERENCES vaccines(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS bookings (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, parent_id INT UNSIGNED NOT NULL, child_id INT UNSIGNED NOT NULL,
 hospital_id INT UNSIGNED NOT NULL, vaccine_id INT UNSIGNED NOT NULL, appointment_date DATE NOT NULL,
 appointment_time TIME NOT NULL, status ENUM('requested','approved','rejected','completed','cancelled') NOT NULL DEFAULT 'requested',
 parent_note TEXT NULL, admin_note TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_booking_parent FOREIGN KEY(parent_id) REFERENCES users(id) ON DELETE CASCADE,
 CONSTRAINT fk_booking_child FOREIGN KEY(child_id) REFERENCES children(id) ON DELETE CASCADE,
 CONSTRAINT fk_booking_hospital FOREIGN KEY(hospital_id) REFERENCES hospitals(id) ON DELETE RESTRICT,
 CONSTRAINT fk_booking_vaccine FOREIGN KEY(vaccine_id) REFERENCES vaccines(id) ON DELETE RESTRICT,
 INDEX(appointment_date), INDEX(status)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS vaccination_records (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, booking_id INT UNSIGNED NOT NULL UNIQUE, child_id INT UNSIGNED NOT NULL,
 vaccine_id INT UNSIGNED NOT NULL, hospital_id INT UNSIGNED NOT NULL, administered_on DATE NOT NULL,
 administered_by INT UNSIGNED NULL, notes TEXT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_record_booking FOREIGN KEY(booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
 CONSTRAINT fk_record_child FOREIGN KEY(child_id) REFERENCES children(id) ON DELETE CASCADE,
 CONSTRAINT fk_record_vaccine FOREIGN KEY(vaccine_id) REFERENCES vaccines(id) ON DELETE RESTRICT,
 CONSTRAINT fk_record_hospital FOREIGN KEY(hospital_id) REFERENCES hospitals(id) ON DELETE RESTRICT,
 CONSTRAINT fk_record_user FOREIGN KEY(administered_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
-- Seed vaccine catalogue. These are illustrative entries; confirm local schedules with a qualified provider.
INSERT IGNORE INTO vaccines(name,dose,recommended_age,description,status) VALUES
('BCG','Dose 1','At birth','Tuberculosis prevention vaccine','available'),
('Polio (OPV)','Birth dose','At birth','Oral polio vaccine','available'),
('Hepatitis B','Dose 1','At birth','Hepatitis B prevention vaccine','available'),
('Pentavalent','Dose 1','6 weeks','Combination vaccine; verify local schedule','available'),
('Pneumococcal (PCV)','Dose 1','6 weeks','Pneumococcal disease prevention','available'),
('Rotavirus','Dose 1','6 weeks','Rotavirus prevention vaccine','available'),
('Measles/Rubella','Dose 1','9 months','Verify local immunisation schedule','available');
-- Create an administrator securely using the one-time script at ../create_admin.php.
