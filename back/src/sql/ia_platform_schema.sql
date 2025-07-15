-- SQL dump for dev_db
CREATE DATABASE IF NOT EXISTS dev_db;
USE dev_db;

CREATE TABLE IF NOT EXISTS `provider` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    presentation TEXT NOT NULL,
    country VARCHAR(100) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    role VARCHAR(50) DEFAULT 'provider',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS availability_slot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    is_booked BOOLEAN NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS completed_work (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    company VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS completed_work_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    work_id INT NOT NULL,
    media_type VARCHAR(50) NOT NULL,
    media_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (work_id) REFERENCES completed_work(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    country VARCHAR(100) NOT NULL,
    role VARCHAR(50) DEFAULT 'client',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    status VARCHAR(100) NOT NULL,
    client_id INT NOT NULL,
    slot_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES availability_slot(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS provided_service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    min_price DECIMAL(10,2) DEFAULT NULL,
    max_price DECIMAL(10,2) DEFAULT NULL,
    duration INT NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `notification` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT NOT NULL,
    recipient_type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS skill (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS provider_skill (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    skill_id INT NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skill(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS request (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    provider_id INT NOT NULL,
    description TEXT NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    provider_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS provider_diploma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    description TEXT NULL,
    start_date DATETIME NULL,
    end_date DATETIME NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);
 
