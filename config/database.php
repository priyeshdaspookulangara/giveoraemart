<?php
// config/database.php

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'giveoraemart_db');
define('DB_USER', 'giveorae');
define('DB_PASS', 'giveorae123');

function get_db_connection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Fallback SQLite in-memory database for dev/testing when MySQL service is offline
            try {
                $pdo = new PDO('sqlite::memory:');
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                $pdo->exec("CREATE TABLE categories (id INTEGER PRIMARY KEY, name TEXT);");
                $pdo->exec("INSERT INTO categories VALUES (1, 'Grocery Essentials'), (2, 'Household & Cookware');");

                $pdo->exec("CREATE TABLE products (id INTEGER PRIMARY KEY, category_id INTEGER, name TEXT, price REAL, image_url TEXT, stock INTEGER, purchase_type TEXT);");
                $pdo->exec("INSERT INTO products VALUES
                    (1, 1, 'Premium Korean Rice 5kg', 24.99, 'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=600&q=80', 50, 'REPURCHASE'),
                    (2, 1, 'Extra Virgin Olive Oil 1L', 14.50, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=600&q=80', 30, 'REPURCHASE'),
                    (3, 2, 'Non-Stick Ceramic Frying Pan 28cm', 39.99, 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?auto=format&fit=crop&w=600&q=80', 15, 'PURCHASE'),
                    (4, 2, 'HEPA Air Purifier Compact', 89.00, 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=600&q=80', 10, 'PURCHASE');");

                $pdo->exec("CREATE TABLE packages (id INTEGER PRIMARY KEY, name TEXT, description TEXT, price REAL, discount_price REAL, image_url TEXT);");
                $pdo->exec("INSERT INTO packages VALUES (1, 'Monthly Family Pantry Package', 'Complete bundle including Korean Rice, Olive Oil, and Seaweed Snacks.', 60.99, 49.99, 'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80');");

                $pdo->exec("CREATE TABLE package_items (id INTEGER PRIMARY KEY, package_id INTEGER, product_id INTEGER, item_name TEXT, quantity INTEGER);");
                $pdo->exec("INSERT INTO package_items VALUES (1, 1, 1, 'Premium Korean Rice 5kg', 1), (2, 1, 2, 'Extra Virgin Olive Oil 1L', 1);");

                $pdo->exec("CREATE TABLE wishlists (id INTEGER PRIMARY KEY, user_id INTEGER, session_id TEXT, item_type TEXT, item_id INTEGER);");
            } catch (Exception $ex) {
                die("Database Connection Error: " . $e->getMessage());
            }
        }
    }
    return $pdo;
}
