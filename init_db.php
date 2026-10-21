<?php
// init_db.php - Seeds and initializes the Giveoraemart MySQL Database

$host = '127.0.0.1';
$user = 'giveorae';
$pass = 'giveorae123';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($sql);
    echo "Database schema imported successfully.\n";

    $pdo->exec("USE `giveoraemart_db`");

    // Seed Admin, Root Matrix Node ("givoratraders") & Sample Customer User
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $customerPassword = password_hash('customer123', PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name)");
    $stmt->execute([1, 'givoratraders', 'givoratraders@giveoraemart.com', $adminPassword, 'ADMIN']);
    $stmt->execute([2, 'Giveorae Admin', 'admin@giveoraemart.com', $adminPassword, 'ADMIN']);
    $stmt->execute([3, 'John Customer', 'john@example.com', $customerPassword, 'CUSTOMER']);

    // Seed Categories
    $categories = [
        ['name' => 'Fresh Groceries', 'slug' => 'fresh-groceries'],
        ['name' => 'Daily Essentials & Repurchase', 'slug' => 'daily-essentials'],
        ['name' => 'Organic Snacks & Drinks', 'slug' => 'organic-snacks'],
        ['name' => 'Home Care & Wellness', 'slug' => 'home-care']
    ];

    $catStmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name)");
    foreach ($categories as $cat) {
        $catStmt->execute([$cat['name'], $cat['slug']]);
    }

    // Fetch Category IDs
    $cats = $pdo->query("SELECT id, slug FROM categories")->fetchAll(PDO::FETCH_KEY_PAIR);

    // Seed Sample Products
    $products = [
        [
            'name' => 'Organic Premium Korean Rice 5kg',
            'slug' => 'organic-korean-rice-5kg',
            'description' => 'Nutritious fresh harvest white rice, perfect for everyday meals and family dining.',
            'price' => 28.50,
            'stock' => 100,
            'category_id' => $cats['daily-essentials'] ?? 2,
            'purchase_type' => 'REPURCHASE',
            'image_url' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=600&q=80'
        ],
        [
            'name' => 'Cold-Pressed Olive Oil 1L',
            'slug' => 'cold-pressed-olive-oil-1l',
            'description' => 'Extra virgin cold-pressed olive oil loaded with healthy fats and antioxidants.',
            'price' => 19.99,
            'stock' => 75,
            'category_id' => $cats['daily-essentials'] ?? 2,
            'purchase_type' => 'REPURCHASE',
            'image_url' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=600&q=80'
        ],
        [
            'name' => 'Artisanal Stainless Steel Cookware Set',
            'slug' => 'artisanal-cookware-set',
            'description' => '5-piece premium non-toxic stainless steel pot and pan set for lifelong cooking.',
            'price' => 149.00,
            'stock' => 25,
            'category_id' => $cats['home-care'] ?? 4,
            'purchase_type' => 'PURCHASE',
            'image_url' => 'https://images.unsplash.com/photo-1584992236310-6edddc08acff?auto=format&fit=crop&w=600&q=80'
        ],
        [
            'name' => 'Roasted Seaweed Snack Pack (12-Count)',
            'slug' => 'roasted-seaweed-pack-12',
            'description' => 'Crispy savory roasted seaweed sheets seasoned with sesame oil and sea salt.',
            'price' => 12.50,
            'stock' => 150,
            'category_id' => $cats['organic-snacks'] ?? 3,
            'purchase_type' => 'REPURCHASE',
            'image_url' => 'https://images.unsplash.com/photo-1608039829572-78524f79c4c7?auto=format&fit=crop&w=600&q=80'
        ],
        [
            'name' => 'Multi-Filter Smart Air Purifier',
            'slug' => 'smart-air-purifier',
            'description' => 'HEPA filtered ultra-quiet smart air purifier with real-time AQI display.',
            'price' => 199.00,
            'stock' => 15,
            'category_id' => $cats['home-care'] ?? 4,
            'purchase_type' => 'PURCHASE',
            'image_url' => 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?auto=format&fit=crop&w=600&q=80'
        ],
        [
            'name' => 'Natural Honey Ginseng Tea 500g',
            'slug' => 'honey-ginseng-tea-500g',
            'description' => 'Traditional restorative honey red ginseng concentrate for daily energy boost.',
            'price' => 24.00,
            'stock' => 60,
            'category_id' => $cats['organic-snacks'] ?? 3,
            'purchase_type' => 'REPURCHASE',
            'image_url' => 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?auto=format&fit=crop&w=600&q=80'
        ]
    ];

    $prodStmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, stock, category_id, purchase_type, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE price=VALUES(price)");
    foreach ($products as $p) {
        $prodStmt->execute([
            $p['name'], $p['slug'], $p['description'], $p['price'], $p['stock'], $p['category_id'], $p['purchase_type'], $p['image_url']
        ]);
    }

    // Seed Sample Packages
    $pkgStmt = $pdo->prepare("INSERT INTO packages (id, name, description, price, discount_price, image_url) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE price=VALUES(price)");
    $pkgStmt->execute([
        1,
        'Monthly Family Repurchase Essentials Bundle',
        'Includes organic 5kg rice, extra virgin olive oil, and roasted seaweed snacks with bonus organic green tea sachet.',
        60.99,
        49.99,
        'https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=600&q=80'
    ]);

    $pkgStmt->execute([
        2,
        'Kitchen Starter Purchase Kit',
        'Complete kitchen outfit featuring our stainless steel cookware set along with premium cold-pressed olive oil and chef apron.',
        175.00,
        149.99,
        'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=600&q=80'
    ]);

    // Seed Package Items (Catalog product references AND ad-hoc inline items)
    $pdo->exec("DELETE FROM package_items");
    $itemStmt = $pdo->prepare("INSERT INTO package_items (package_id, product_id, item_name, quantity) VALUES (?, ?, ?, ?)");

    // Package 1 items:
    // Catalog item: Rice (ID 1)
    $itemStmt->execute([1, 1, NULL, 1]);
    // Catalog item: Olive oil (ID 2)
    $itemStmt->execute([1, 2, NULL, 1]);
    // Ad-hoc inline item: Organic Green Tea Sachet 10s
    $itemStmt->execute([1, NULL, 'Organic Green Tea Sachet (10-Count Bonus)', 1]);

    // Package 2 items:
    // Catalog item: Cookware set (ID 3)
    $itemStmt->execute([2, 3, NULL, 1]);
    // Catalog item: Olive oil (ID 2)
    $itemStmt->execute([2, 2, NULL, 1]);
    // Ad-hoc inline item: Premium Cotton Kitchen Apron
    $itemStmt->execute([2, NULL, 'Signature Master Chef Cotton Apron', 1]);

    echo "Initial database seeding completed successfully.\n";

} catch (PDOException $e) {
    die("Database Initialization Failed: " . $e->getMessage() . "\n");
}
