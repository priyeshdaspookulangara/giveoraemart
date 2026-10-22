-- Charity & CMS Database Schema
CREATE DATABASE IF NOT EXISTS `charity_cms_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `charity_cms_db`;

-- 1. Users Table
DROP TABLE IF EXISTS `donations`;
DROP TABLE IF EXISTS `cms_posts`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'editor', 'donor', 'beneficiary') NOT NULL DEFAULT 'donor',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Campaigns Table
DROP TABLE IF EXISTS `campaigns`;

CREATE TABLE `campaigns` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `goal_amount` DECIMAL(10,2) NOT NULL,
  `raised_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('active', 'completed', 'paused') NOT NULL DEFAULT 'active',
  `image_url` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Donations Table
CREATE TABLE `donations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `donor_id` INT DEFAULT NULL,
  `campaign_id` INT NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `transaction_id` VARCHAR(100) NOT NULL,
  `payment_status` ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'completed',
  `donated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_donations_donor` FOREIGN KEY (`donor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_donations_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Beneficiaries Table
DROP TABLE IF EXISTS `beneficiaries`;

CREATE TABLE `beneficiaries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `contact_number` VARCHAR(50) NOT NULL,
  `assistance_type` VARCHAR(100) NOT NULL,
  `requested_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `details` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. CMS Pages Table
DROP TABLE IF EXISTS `cms_pages`;

CREATE TABLE `cms_pages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `content` LONGTEXT NOT NULL,
  `status` ENUM('published', 'draft') NOT NULL DEFAULT 'published',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. CMS Posts Table
CREATE TABLE `cms_posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `author_id` INT NOT NULL,
  `content` LONGTEXT NOT NULL,
  `featured_image` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('published', 'draft') NOT NULL DEFAULT 'published',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Site Settings Table
DROP TABLE IF EXISTS `site_settings`;

CREATE TABLE `site_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Data Insertion
-- Users (Password for all default accounts is 'admin123' / 'donor123')
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'System Admin', 'admin@charity.org', '$2y$10$f90zGqJ1sF402c9vR7W32eO63jD7mU6H4V9k1B1Y5m8K8Z0B0Y1G2', 'admin'),
(2, 'Content Editor', 'editor@charity.org', '$2y$10$f90zGqJ1sF402c9vR7W32eO63jD7mU6H4V9k1B1Y5m8K8Z0B0Y1G2', 'editor'),
(3, 'John Donor', 'donor@charity.org', '$2y$10$f90zGqJ1sF402c9vR7W32eO63jD7mU6H4V9k1B1Y5m8K8Z0B0Y1G2', 'donor'),
(4, 'Maria Beneficiary', 'maria@charity.org', '$2y$10$f90zGqJ1sF402c9vR7W32eO63jD7mU6H4V9k1B1Y5m8K8Z0B0Y1G2', 'beneficiary');

-- Campaigns
INSERT INTO `campaigns` (`id`, `title`, `slug`, `description`, `goal_amount`, `raised_amount`, `status`, `image_url`) VALUES
(1, 'Clean Water for Rural Communities', 'clean-water-project', 'Providing sustainable clean water filtration units and solar-powered wells to rural villages lacking access to safe drinking water.', 15000.00, 8450.00, 'active', 'https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?auto=format&fit=crop&w=800&q=80'),
(2, 'Emergency Medical Care & Relief', 'emergency-medical-care', 'Funding essential surgeries, emergency vaccinations, and mobile clinic visits for families living in underserved regions.', 25000.00, 18200.00, 'active', 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?auto=format&fit=crop&w=800&q=80'),
(3, 'Children\'s Education & School Supplies', 'children-education-fund', 'Equipping remote schools with textbooks, digital learning tablets, and daily nutritious meals to keep children in school.', 10000.00, 4100.00, 'active', 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=800&q=80');

-- Donations
INSERT INTO `donations` (`id`, `donor_id`, `campaign_id`, `amount`, `transaction_id`, `payment_status`) VALUES
(1, 3, 1, 250.00, 'TXN-98213401', 'completed'),
(2, NULL, 1, 100.00, 'TXN-98213402', 'completed'),
(3, 3, 2, 500.00, 'TXN-98213403', 'completed'),
(4, NULL, 3, 50.00, 'TXN-98213404', 'completed');

-- Beneficiaries
INSERT INTO `beneficiaries` (`id`, `full_name`, `contact_number`, `assistance_type`, `requested_amount`, `status`, `details`) VALUES
(1, 'Sarah Jenkins', '+1 555-0143', 'Medical', 2500.00, 'pending', 'Requesting financial support for urgent specialized cardiac procedure.'),
(2, 'David Miller', '+1 555-0188', 'Education', 1200.00, 'approved', 'Tuition support for final semester in community health nursing.');

-- CMS Pages
INSERT INTO `cms_pages` (`id`, `title`, `slug`, `content`, `status`) VALUES
(1, 'About Us', 'about-us', '<h2>Our Mission & Vision</h2><p>HopeCare Charity Foundation is dedicated to transforming lives through direct, transparent, and impactful community programs. We believe every human being deserves clean water, accessible healthcare, and quality education.</p><h3>How We Work</h3><p>We work directly with grassroots community leaders to identify urgent needs, allocate donor funds directly, and report back with transparent metrics.</p>', 'published'),
(2, 'Privacy Policy', 'privacy-policy', '<h2>Privacy Policy</h2><p>At HopeCare, we protect your personal and payment details with enterprise-grade encryption. We never sell, rent, or trade your donor information to third parties.</p>', 'published'),
(3, 'Impact Stories', 'impact-stories', '<h2>Real Stories, Real Impact</h2><p>Thanks to our generous donors, over 12,000 families across 45 villages now have access to clean drinking water and emergency medical aid.</p>', 'published');

-- CMS Posts
INSERT INTO `cms_posts` (`id`, `title`, `slug`, `author_id`, `content`, `featured_image`, `status`) VALUES
(1, 'How Clean Water Changes Lives in Remote Villages', 'how-clean-water-changes-lives', 1, '<p>Access to clean water reduces waterborne illnesses by over 80% and allows children—especially young girls—to attend school instead of walking miles every morning to fetch water.</p><p>Read how our recent clean water installation in Eastern District transformed the community.</p>', 'https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?auto=format&fit=crop&w=800&q=80', 'published'),
(2, 'Annual Charity Gala Raises Over $50,000 for Medical Relief', 'annual-charity-gala-raises-over-50k', 2, '<p>Our annual fundraising gala was an inspiring success thanks to our dedicated community of donors and volunteers!</p>', 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=800&q=80', 'published');

-- Site Settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_title', 'HopeCare Charity Foundation'),
('contact_email', 'contact@hopecarecharity.org'),
('helpline_number', '+1 (800) 555-0199'),
('footer_text', '© 2025 HopeCare Charity Foundation. Empowering communities worldwide.'),
('currency_symbol', '$'),
('hero_title', 'Empower Lives, Inspire Hope'),
('hero_subtitle', 'Join our mission to provide clean water, healthcare, and education to underprivileged communities around the world.');
