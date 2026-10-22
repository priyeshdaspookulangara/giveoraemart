<?php
// init_db.php - Initializes Charity CMS MySQL Database and Seeds Initial Data

$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$port = getenv('DB_PORT') ?: 3306;

echo "Initializing Charity CMS Database...\n";

try {
    // Try connecting without selecting database first
    $mysqli = new mysqli($host, $user, $pass, "", (int)$port);
    if ($mysqli->connect_error) {
        throw new Exception("Connection error: " . $mysqli->connect_error);
    }

    $sqlContent = file_get_contents(__DIR__ . '/schema.sql');

    // Execute multi query or statements split
    if ($mysqli->multi_query($sqlContent)) {
        do {
            /* store first result set */
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());
    }

    if ($mysqli->errno) {
        throw new Exception("Query error: " . $mysqli->error);
    }

    echo "Database 'charity_cms_db' and tables created and seeded successfully!\n";

} catch (Exception $e) {
    echo "Notice/Warning during init: " . $e->getMessage() . "\n";
    echo "Database initialization script executed.\n";
}
