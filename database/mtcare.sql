-- =============================================================
--  My Trusted Care (MTCare) - Sierra Leone Digital Health Gateway
--  MySQL Database Schema + Seed Data
--  Compatible with MySQL 5.7+ / MariaDB 10+ / PHP 8+
-- =============================================================

CREATE DATABASE IF NOT EXISTS mtcare_db
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE mtcare_db;

-- Clean slate (drop in dependency order) -----------------------
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS symptom_rules;
DROP TABLE IF EXISTS doctors;
DROP TABLE IF EXISTS hospitals;
DROP TABLE IF EXISTS symptoms;
DROP TABLE IF EXISTS areas;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS users;

-- =============================================================
--  USERS  (patients + administrators)
-- =============================================================
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(120)  NOT NULL,
    phone         VARCHAR(40)   DEFAULT NULL,
    email         VARCHAR(160)  NOT NULL UNIQUE,
    password_hash VARCHAR(255)  NOT NULL,
    role          ENUM('patient','admin') NOT NULL DEFAULT 'patient',
    created_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================================
--  AREAS  (Provinces / Regions of Sierra Leone)
-- =============================================================
CREATE TABLE areas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    description VARCHAR(200) DEFAULT NULL
) ENGINE=InnoDB;

-- =============================================================
--  HOSPITALS
-- =============================================================
CREATE TABLE hospitals (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    name     VARCHAR(160) NOT NULL,
    area_id  INT DEFAULT NULL,
    address  VARCHAR(200) DEFAULT NULL,
    phone    VARCHAR(40)  DEFAULT NULL,
    CONSTRAINT fk_hospital_area FOREIGN KEY (area_id)
        REFERENCES areas(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================================
--  DOCTORS
-- =============================================================
CREATE TABLE doctors (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(160) NOT NULL,
    specialty    VARCHAR(120) NOT NULL,
    hospital_id  INT DEFAULT NULL,
    active       TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT fk_doctor_hospital FOREIGN KEY (hospital_id)
        REFERENCES hospitals(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================================
--  SYMPTOMS
-- =============================================================
CREATE TABLE symptoms (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    name  VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- =============================================================
--  SYMPTOM RULES  (maps a symptom -> recommended clinical test)
-- =============================================================
CREATE TABLE symptom_rules (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    symptom_id        INT NOT NULL,
    recommended_test  VARCHAR(160) NOT NULL,
    advice            VARCHAR(255) DEFAULT NULL,
    CONSTRAINT fk_rule_symptom FOREIGN KEY (symptom_id)
        REFERENCES symptoms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================================
--  APPOINTMENTS
-- =============================================================
CREATE TABLE appointments (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    patient_id       INT NOT NULL,
    doctor_id        INT DEFAULT NULL,
    hospital_id      INT DEFAULT NULL,
    appointment_date DATE NOT NULL,
    time_slot        VARCHAR(20) NOT NULL,
    status           ENUM('Pending','Confirmed','Completed','Cancelled')
                        NOT NULL DEFAULT 'Pending',
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_appt_patient  FOREIGN KEY (patient_id)  REFERENCES users(id)     ON DELETE CASCADE,
    CONSTRAINT fk_appt_doctor   FOREIGN KEY (doctor_id)   REFERENCES doctors(id)   ON DELETE SET NULL,
    CONSTRAINT fk_appt_hospital FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =============================================================
--  CONTACT MESSAGES  (Contact Us form)
-- =============================================================
CREATE TABLE contact_messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    full_name  VARCHAR(120) NOT NULL,
    email      VARCHAR(160) NOT NULL,
    message    TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================================
--  SEED DATA
-- =============================================================

-- Users -------------------------------------------------------
-- NOTE: password for every seeded account is  "password123"
-- (bcrypt hash generated with PHP password_hash()).
INSERT INTO users (full_name, phone, email, password_hash, role) VALUES
('Admin User',      '+232 76 000000', 'admin@mtcare.com',  '$2y$12$ArcgxaNTm62BUCUNgg3Cee/07NDbT7R9b2osZW52m4ODzL1DtEmc.', 'admin'),
('Sahr Kamara',     '+232 77 987654', 'sahr@gmail.com',    '$2y$12$ArcgxaNTm62BUCUNgg3Cee/07NDbT7R9b2osZW52m4ODzL1DtEmc.', 'patient'),
('Fatmata Bangura', '+232 76 445566', 'fatmata@gmail.com', '$2y$12$ArcgxaNTm62BUCUNgg3Cee/07NDbT7R9b2osZW52m4ODzL1DtEmc.', 'patient');

-- Areas -------------------------------------------------------
INSERT INTO areas (name, description) VALUES
('Western Area Urban (Freetown)',        'Capital city metropolitan zone'),
('Western Area Rural (Waterloo, Lungi)', 'Rural districts around the capital'),
('Eastern Province (Kenema, Kono)',      'Eastern diamond & agriculture belt'),
('Northern Province (Makeni)',           'Northern regional hub'),
('Southern Province (Bo)',               'Southern regional centre'),
('North-West Province (Port Loko)',      'North-western coastal districts');

-- Hospitals ---------------------------------------------------
INSERT INTO hospitals (name, area_id, address, phone) VALUES
('Connaught Hospital',            1, 'Percival Street, Central Freetown', '+232 76 111222'),
('Ola During Children''s Hospital',1, 'Fourah Bay Road, Freetown',        '+232 76 222333'),
('Makeni Regional Hospital',      4, 'Azzolini Highway, Makeni',          '+232 76 333444'),
('Bo Government Hospital',        5, 'Bojon Street, Bo',                  '+232 76 444555'),
('Kenema Government Hospital',    3, 'Hangha Road, Kenema',               '+232 76 555666'),
('Port Loko District Hospital',   6, 'Gbonkomanie, Port Loko',            '+232 76 666777');

-- Doctors -----------------------------------------------------
INSERT INTO doctors (name, specialty, hospital_id, active) VALUES
('Dr. Zainab Conteh',    'General Practitioner', 1, 1),
('Dr. Josephine Sallu',  'Pediatrician',         2, 1),
('Dr. Aminata Sesay',    'General Practitioner', 3, 1),
('Dr. Mohamed Kargbo',   'Internal Medicine',    4, 1),
('Dr. Ibrahim Koroma',   'Infectious Disease',   5, 1),
('Dr. Isata Turay',      'Family Medicine',      6, 1),
('Dr. Alusine Bangura',  'General Surgery',      1, 1);

-- Symptoms ----------------------------------------------------
INSERT INTO symptoms (name) VALUES
('Fever'),       -- 1
('Headache'),    -- 2
('Cough'),       -- 3
('Body Pain'),   -- 4
('Stomach Pain'),-- 5
('Chest Pain'),  -- 6
('Sore Throat'), -- 7
('Diarrhea');    -- 8

-- Symptom Rules (symptom -> recommended clinical test) ---------
INSERT INTO symptom_rules (symptom_id, recommended_test, advice) VALUES
(1, 'Malaria Rapid Diagnostic Test (RDT) + Widal Assay', 'Fever is a primary indicator for malaria and typhoid screening.'),
(2, 'Full Blood Count (CBC) + Blood Pressure Check',      'Persistent headaches warrant a CBC and BP evaluation.'),
(3, 'Chest Examination + Sputum Test',                    'A lingering cough should be reviewed for respiratory infection.'),
(4, 'Full Blood Count (CBC) + Malaria RDT',               'Generalised body pain is commonly linked to malaria.'),
(5, 'Abdominal Ultrasound + Widal Assay',                 'Stomach pain requires typhoid screening and imaging.'),
(6, 'ECG + Chest X-Ray',                                  'Chest pain must be assessed urgently for cardiac causes.'),
(7, 'Throat Swab Culture',                                'Sore throat may indicate a bacterial infection.'),
(8, 'Stool Analysis + Hydration Assessment',              'Diarrhea needs stool testing and hydration monitoring.');

-- Appointments ------------------------------------------------
INSERT INTO appointments (patient_id, doctor_id, hospital_id, appointment_date, time_slot, status) VALUES
(2, 1, 1, '2026-06-18', '10:30 AM', 'Confirmed'),
(2, 2, 2, '2026-06-20', '02:00 PM', 'Pending'),
(3, 3, 3, '2026-06-16', '09:00 AM', 'Completed');
