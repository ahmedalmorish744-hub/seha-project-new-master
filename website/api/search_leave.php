<?php
/**
 * Search Leave API Endpoint
 * نقطة الاستعلام عن إجازة مرضية
 *
 * GET /api/search_leave.php?service_number=XXX&id_number=YYY
 */

require_once __DIR__ . '/config.php';

// Only accept GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use GET.'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$service_number = isset($_GET['service_number']) ? trim($_GET['service_number']) : '';
$id_number = isset($_GET['id_number']) ? trim($_GET['id_number']) : '';

if (empty($service_number) && empty($id_number)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please provide service_number or id_number'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

try {
    $db = getDB();

    if (!empty($service_number) && !empty($id_number)) {
        // Search by both service number and ID number
        $stmt = $db->prepare('SELECT * FROM leaves WHERE leave_number = :leave_number AND id_number = :id_number ORDER BY created_at DESC');
        $stmt->execute([
            ':leave_number' => $service_number,
            ':id_number' => $id_number
        ]);
    } elseif (!empty($service_number)) {
        // Search by service number only
        $stmt = $db->prepare('SELECT * FROM leaves WHERE leave_number = :leave_number ORDER BY created_at DESC');
        $stmt->execute([':leave_number' => $service_number]);
    } else {
        // Search by ID number only
        $stmt = $db->prepare('SELECT * FROM leaves WHERE id_number = :id_number ORDER BY created_at DESC');
        $stmt->execute([':id_number' => $id_number]);
    }

    $results = $stmt->fetchAll();

    if (!empty($results)) {
        // Format results for display
        $formatted = array_map(function($row) {
            return [
                'leaveNumber' => $row['leave_number'],
                'idNumber' => $row['id_number'],
                'name' => $row['name'],
                'reportDate' => $row['report_date'],
                'entryDate' => $row['entry_date'],
                'exitDate' => $row['exit_date'],
                'doctor' => $row['doctor'],
                'jobTitle' => $row['job_title'],
                'createdAt' => $row['created_at']
            ];
        }, $results);

        echo json_encode([
            'success' => true,
            'message' => 'Records found',
            'count' => count($formatted),
            'data' => $formatted
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No records found matching your search criteria',
            'count' => 0,
            'data' => []
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
