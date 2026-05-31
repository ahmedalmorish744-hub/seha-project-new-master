<?php
/**
 * Add Leave API Endpoint
 * نقطة إضافة إجازة مرضية
 *
 * POST /api/add_leave.php
 * Body: JSON { leaveNumber, idNumber, name, reportDate, entryDate, exitDate, doctor, jobTitle }
 */

require_once __DIR__ . '/config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Read JSON input
$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);

if (!$data) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Validate required fields
$required = ['leaveNumber', 'idNumber', 'name'];
$missing = [];
foreach ($required as $field) {
    if (empty($data[$field])) {
        $missing[] = $field;
    }
}

if (!empty($missing)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields: ' . implode(', ', $missing)
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    $db = getDB();

    // Check if leave already exists
    $stmt = $db->prepare('SELECT id FROM leaves WHERE leave_number = :leave_number');
    $stmt->execute([':leave_number' => $data['leaveNumber']]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Update existing record
        $stmt = $db->prepare('UPDATE leaves SET
            id_number = :id_number,
            name = :name,
            report_date = :report_date,
            entry_date = :entry_date,
            exit_date = :exit_date,
            doctor = :doctor,
            job_title = :job_title
            WHERE leave_number = :leave_number');

        $stmt->execute([
            ':id_number' => $data['idNumber'],
            ':name' => $data['name'],
            ':report_date' => $data['reportDate'] ?? null,
            ':entry_date' => $data['entryDate'] ?? null,
            ':exit_date' => $data['exitDate'] ?? null,
            ':doctor' => $data['doctor'] ?? null,
            ':job_title' => $data['jobTitle'] ?? null,
            ':leave_number' => $data['leaveNumber']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Leave record updated successfully',
            'data' => [
                'leave_number' => $data['leaveNumber'],
                'action' => 'updated'
            ]
        ], JSON_UNESCAPED_UNICODE);

    } else {
        // Insert new record
        $stmt = $db->prepare('INSERT INTO leaves
            (leave_number, id_number, name, report_date, entry_date, exit_date, doctor, job_title)
            VALUES
            (:leave_number, :id_number, :name, :report_date, :entry_date, :exit_date, :doctor, :job_title)');

        $stmt->execute([
            ':leave_number' => $data['leaveNumber'],
            ':id_number' => $data['idNumber'],
            ':name' => $data['name'],
            ':report_date' => $data['reportDate'] ?? null,
            ':entry_date' => $data['entryDate'] ?? null,
            ':exit_date' => $data['exitDate'] ?? null,
            ':doctor' => $data['doctor'] ?? null,
            ':job_title' => $data['jobTitle'] ?? null
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Leave record added successfully',
            'data' => [
                'leave_number' => $data['leaveNumber'],
                'id' => $db->lastInsertId(),
                'action' => 'created'
            ]
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
