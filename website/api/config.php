<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$db_path = __DIR__ . '/../data/leaves.db';

if (!file_exists(__DIR__ . '/../data')) {
    mkdir(__DIR__ . '/../data', 0777, true);
}

try {
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->busyTimeout(5000);
    $db->exec('PRAGMA journal_mode=WAL');

    $db->exec('CREATE TABLE IF NOT EXISTS leaves (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        leave_number TEXT UNIQUE NOT NULL,
        id_number TEXT NOT NULL,
        name TEXT NOT NULL,
        report_date TEXT NOT NULL,
        entry_date TEXT NOT NULL,
        exit_date TEXT NOT NULL,
        doctor TEXT NOT NULL,
        job_title TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    $db->exec('CREATE INDEX IF NOT EXISTS idx_leave_number ON leaves(leave_number)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_id_number ON leaves(id_number)');

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit();
}
?>
