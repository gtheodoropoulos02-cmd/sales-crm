-- Sales CRM - Database Installation
-- Run this file once to set up the database

CREATE DATABASE IF NOT EXISTS sales_crm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sales_crm;

-- Users table (admins and partners)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin','partner') NOT NULL DEFAULT 'partner',
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB;

-- Providers (linked to category)
CREATE TABLE IF NOT EXISTS providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Products (linked to provider)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Statuses
CREATE TABLE IF NOT EXISTS statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(20) DEFAULT 'gray'
) ENGINE=InnoDB;

-- Applications
CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    app_code VARCHAR(20) NOT NULL UNIQUE,
    partner_id INT NOT NULL,
    status_id INT NOT NULL,
    category_id INT,
    provider_id INT,
    product_id INT,
    -- Customer data
    customer_type VARCHAR(30) DEFAULT 'ΙΔΙΩΤΗΣ',
    connection_type VARCHAR(50),
    ebill VARCHAR(10),
    cust_firstname VARCHAR(100),
    cust_lastname VARCHAR(100),
    cust_patronimo VARCHAR(100),
    cust_adt VARCHAR(30),
    cust_birthdate DATE,
    cust_afm VARCHAR(20),
    cust_doy VARCHAR(100),
    cust_tk VARCHAR(10),
    cust_nomos VARCHAR(100),
    cust_poli VARCHAR(100),
    cust_periochi VARCHAR(100),
    cust_address VARCHAR(200),
    cust_number VARCHAR(20),
    cust_phone VARCHAR(20),
    cust_kinito VARCHAR(20),
    cust_email VARCHAR(100),
    -- Contact person
    contact_name VARCHAR(100),
    contact_lastname VARCHAR(100),
    contact_patronimo VARCHAR(100),
    contact_adt VARCHAR(30),
    contact_phone VARCHAR(20),
    contact_kinito VARCHAR(20),
    contact_email VARCHAR(100),
    -- Program data
    prog_phone_activation VARCHAR(30),
    prog_sim_activation VARCHAR(50),
    prog_paketo VARCHAR(100),
    prog_timi VARCHAR(20),
    prog_anypsos VARCHAR(50),
    prog_sim_received VARCHAR(10),
    prog_consent1 VARCHAR(30),
    prog_consent2 VARCHAR(30),
    prog_signature_way VARCHAR(30),
    prog_ebill VARCHAR(10),
    notes TEXT,
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (partner_id) REFERENCES users(id),
    FOREIGN KEY (status_id) REFERENCES statuses(id)
) ENGINE=InnoDB;

-- Documents uploaded per application
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    doc_type ENUM('identity','logariasmos','bebaiosi','extra') NOT NULL,
    original_name VARCHAR(255),
    stored_name VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- InfoPortal companies & plans
CREATE TABLE IF NOT EXISTS info_companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS info_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    price VARCHAR(100),
    docs TEXT,
    FOREIGN KEY (company_id) REFERENCES info_companies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Default admin user (password: admin123)
INSERT INTO users (name, username, password, role) VALUES
('Διαχειριστής', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Default statuses
INSERT INTO statuses (name, color) VALUES
('Προς Έλεγχο', 'blue'),
('Ενεργοποιημένη', 'green'),
('Εκκρεμότητα', 'yellow'),
('Απορρίφθηκε', 'red');

-- Note: Default password for admin is 'password'
-- Change it immediately after installation via the admin panel
