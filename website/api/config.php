<?php
/**
 * Database Configuration and Connection
 * إعداد قاعدة البيانات والاتصال
 */

// CORS Headers - Allow access from anywhere
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database path
define('DB_PATH', __DIR__ . '/../data/leaves.db');

/**
 * Get database connection
 * الحصول على اتصال قاعدة البيانات
 */
function getDB() {
    static $db = null;

    if ($db === null) {
        try {
            // Create data directory if it doesn't exist
            $dataDir = dirname(DB_PATH);
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0777, true);
            }

            $db = new PDO('sqlite:' . DB_PATH);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->exec('PRAGMA journal_mode=WAL');
            $db->exec('PRAGMA foreign_keys=ON');

            // Create leaves table if not exists
            $db->exec('CREATE TABLE IF NOT EXISTS leaves (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                leave_number TEXT NOT NULL UNIQUE,
                id_number TEXT NOT NULL,
                name TEXT NOT NULL,
                report_date TEXT,
                entry_date TEXT,
                exit_date TEXT,
                doctor TEXT,
                job_title TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )');

            // Create index for faster searches
            $db->exec('CREATE INDEX IF NOT EXISTS idx_id_number ON leaves(id_number)');
            $db->exec('CREATE INDEX IF NOT EXISTS idx_leave_number ON leaves(leave_number)');

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    return $db;
}
