-- Schéma de la base ia_platform aligné avec les entités et repositories

DROP DATABASE IF EXISTS ia_platform;
CREATE DATABASE ia_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ia_platform;

-- ========================================
-- TABLES DE BASE (sans dépendances)
-- ========================================

CREATE TABLE IF NOT EXISTS `country` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS `job` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS `language` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS `tag` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `soft_skill` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS `hard_skill` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL
);

-- ========================================
-- TABLES PRINCIPALES (dépendent des tables de base)
-- ========================================

CREATE TABLE IF NOT EXISTS `provider` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    slug VARCHAR(255) NOT NULL,
    job_id INT NOT NULL,
    country_id INT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) DEFAULT NULL,
    postal_code VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    FOREIGN KEY (job_id) REFERENCES job(id),
    FOREIGN KEY (country_id) REFERENCES country(id)
);

CREATE TABLE IF NOT EXISTS `client` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT NULL,
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    slug VARCHAR(255) NOT NULL,
    country_id INT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) DEFAULT NULL,
    postal_code VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    FOREIGN KEY (country_id) REFERENCES country(id)
);

-- ========================================
-- TABLES DÉPENDANTES DES PROVIDERS/CLIENTS
-- ========================================

CREATE TABLE IF NOT EXISTS `service` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    max_price DECIMAL(10,2) DEFAULT NULL,
    min_price DECIMAL(10,2) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    cover VARCHAR(255) DEFAULT NULL,
    summary TEXT,
    slug VARCHAR(255) NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `article` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    language_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    published_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    summary TEXT,
    is_published BOOLEAN DEFAULT FALSE,
    is_featured BOOLEAN DEFAULT FALSE,
    cover VARCHAR(255) DEFAULT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES language(id)
);

CREATE TABLE IF NOT EXISTS `location` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_holder_id INT NOT NULL,
    account_holder_type VARCHAR(50) NOT NULL,
    country_id INT NOT NULL,
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) DEFAULT NULL,
    postal_code VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (country_id) REFERENCES country(id)
);

CREATE TABLE IF NOT EXISTS `education` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    institution_name VARCHAR(255) NOT NULL,
    description TEXT,
    started_at DATE NOT NULL,
    ended_at DATE DEFAULT NULL,
    institution_image VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `experience` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    first_task TEXT NOT NULL,
    second_task TEXT DEFAULT NULL,
    third_task TEXT DEFAULT NULL,
    started_at DATE NOT NULL,
    ended_at DATE DEFAULT NULL,
    company_logo VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `social_link` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    platform VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `provider_language` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    language_id INT NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES language(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `provider_soft_skills` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    soft_skill_id INT NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE,
    FOREIGN KEY (soft_skill_id) REFERENCES soft_skill(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `provider_hard_skills` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    hard_skill_id INT NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE,
    FOREIGN KEY (hard_skill_id) REFERENCES hard_skill(id) ON DELETE CASCADE
);

-- ========================================
-- TABLES DE CONTENU (dépendent des services/articles)
-- ========================================

CREATE TABLE IF NOT EXISTS `service_section` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    FOREIGN KEY (service_id) REFERENCES service(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `service_content` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_content_id INT NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (service_content_id) REFERENCES service_section(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `service_image` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_content_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    FOREIGN KEY (service_content_id) REFERENCES service_content(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `article_section` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    FOREIGN KEY (article_id) REFERENCES article(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `article_content` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_content_id INT NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (article_content_id) REFERENCES article_section(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `article_image` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_content_id INT NOT NULL,
    url VARCHAR(255) NOT NULL,
    FOREIGN KEY (article_content_id) REFERENCES article_content(id) ON DELETE CASCADE
);

-- ========================================
-- TABLES DE LIAISON (services/articles/tags)
-- ========================================

CREATE TABLE IF NOT EXISTS `service_tag` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (service_id) REFERENCES service(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE,
    UNIQUE KEY unique_service_tag (service_id, tag_id)
);

CREATE TABLE IF NOT EXISTS `article_tag` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (article_id) REFERENCES article(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tag(id) ON DELETE CASCADE,
    UNIQUE KEY unique_article_tag (article_id, tag_id)
);

-- ========================================
-- RÉSERVATION, DEMANDES, AVIS, NOTIFS
-- ========================================

CREATE TABLE IF NOT EXISTS availability_slot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    is_booked BOOLEAN NOT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
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

CREATE TABLE IF NOT EXISTS request (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    provider_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'pending',
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

CREATE TABLE IF NOT EXISTS `notification` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT NOT NULL,
    recipient_type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ========================================
-- PORTFOLIO (travaux réalisés)
-- ========================================

CREATE TABLE IF NOT EXISTS completed_work (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    company VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE DEFAULT NULL,
    FOREIGN KEY (provider_id) REFERENCES provider(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS completed_work_media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    completed_work_id INT NOT NULL,
    media_type ENUM('image','video') NOT NULL,
    media_url VARCHAR(1024) NOT NULL,
    FOREIGN KEY (completed_work_id) REFERENCES completed_work(id) ON DELETE CASCADE
);