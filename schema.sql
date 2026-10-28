CREATE DATABASE IF NOT EXISTS `giveoraemart_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `giveoraemart_db`;

DROP TABLE IF EXISTS `commissions`;
DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `package_items`;
DROP TABLE IF EXISTS `packages`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `phone` VARCHAR(50) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('ADMIN', 'CUSTOMER') NOT NULL DEFAULT 'CUSTOMER',
    `sponsor_id` INT DEFAULT NULL,
    `matrix_parent_id` INT DEFAULT NULL,
    `matrix_position` TINYINT DEFAULT NULL,
    `txn_id` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`sponsor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`matrix_parent_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `stock` INT NOT NULL DEFAULT 0,
    `category_id` INT,
    `purchase_type` ENUM('PURCHASE', 'REPURCHASE') NOT NULL DEFAULT 'PURCHASE',
    `image_url` VARCHAR(500),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `packages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `discount_price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `image_url` VARCHAR(500),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `package_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `package_id` INT NOT NULL,
    `product_id` INT DEFAULT NULL,
    `item_name` VARCHAR(255) DEFAULT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    FOREIGN KEY (`package_id`) REFERENCES `packages`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT DEFAULT NULL,
    `guest_name` VARCHAR(255) DEFAULT NULL,
    `guest_email` VARCHAR(255) DEFAULT NULL,
    `guest_phone` VARCHAR(50) DEFAULT NULL,
    `shipping_address` TEXT NOT NULL,
    `total_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `payment_method` ENUM('COD', 'UPI') NOT NULL DEFAULT 'COD',
    `txn_id` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('PENDING', 'PAID', 'SHIPPED', 'DELIVERED') NOT NULL DEFAULT 'PENDING',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `item_type` ENUM('PRODUCT', 'PACKAGE') NOT NULL,
    `item_id` INT NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `price` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `wishlists` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT DEFAULT NULL,
    `session_id` VARCHAR(255) DEFAULT NULL,
    `item_type` ENUM('PRODUCT', 'PACKAGE') NOT NULL,
    `item_id` INT NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `commissions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `from_user_id` INT NOT NULL,
    `order_id` INT DEFAULT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `income_type` ENUM('MATRIX', 'LEVEL') NOT NULL,
    `level` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`from_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
