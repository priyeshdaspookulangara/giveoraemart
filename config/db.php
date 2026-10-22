<?php
// config/db.php - Reusable MySQLi Database Connection & Helpers

if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'charity_cms_db');
if (!defined('DB_PORT')) define('DB_PORT', 3306);

/**
 * Returns a singleton MySQLi database connection.
 * Sets utf8mb4 charset and robust error reporting.
 *
 * @return mysqli|null
 */
function get_db_connection() {
    static $conn = null;
    static $failed = false;

    if ($failed) {
        return null;
    }

    if ($conn === null) {
        // Enable MySQLi exception reporting
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            $conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log("Database Connection Warning: " . $e->getMessage());
            $conn = null;
            $failed = true;
        }
    }
    return $conn;
}

/**
 * Helper function to execute prepared MySQLi statements safely.
 *
 * @param string $sql
 * @param array $params
 * @param string $types
 * @return array|int|bool
 */
function db_execute($sql, $params = [], $types = "") {
    try {
        $conn = get_db_connection();
        if (!$conn) {
            return (stripos(trim($sql), 'SELECT') === 0 || stripos(trim($sql), 'SHOW') === 0) ? [] : false;
        }

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return (stripos(trim($sql), 'SELECT') === 0 || stripos(trim($sql), 'SHOW') === 0) ? [] : false;
        }

        if (!empty($params)) {
            if (empty($types)) {
                $types = "";
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= "i";
                    } elseif (is_double($param) || is_float($param)) {
                        $types .= "d";
                    } else {
                        $types .= "s";
                    }
                }
            }
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        if (stripos(trim($sql), 'SELECT') === 0 || stripos(trim($sql), 'SHOW') === 0) {
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $stmt->close();
            return $rows;
        } else {
            $affectedRows = $stmt->affected_rows;
            $insertId = $stmt->insert_id;
            $stmt->close();
            return $insertId > 0 ? $insertId : $affectedRows;
        }
    } catch (Exception $e) {
        error_log("Database Query Exception: " . $e->getMessage());
        return (stripos(trim($sql), 'SELECT') === 0 || stripos(trim($sql), 'SHOW') === 0) ? [] : false;
    }
}

/**
 * Fetch single row result helper
 */
function db_fetch_one($sql, $params = [], $types = "") {
    $rows = db_execute($sql, $params, $types);
    return !empty($rows) && is_array($rows) ? $rows[0] : null;
}
