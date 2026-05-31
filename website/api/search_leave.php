<?php
require_once 'config.php';

try {
    $leave_number = isset($_GET['leave_number']) ? trim($_GET['leave_number']) : '';
    $id_number = isset($_GET['id_number']) ? trim($_GET['id_number']) : '';
    
    if (empty($leave_number) && empty($id_number)) {
        echo json_encode(['success' => false, 'message' => 'Please enter leave number or ID number']);
        exit();
    }
    
    if (!empty($leave_number) && !empty($id_number)) {
        $stmt = $db->prepare('SELECT * FROM leaves WHERE leave_number = :leave_number AND id_number = :id_number ORDER BY created_at DESC');
        $stmt->bindParam(':leave_number', $leave_number);
        $stmt->bindParam(':id_number', $id_number);
    } elseif (!empty($leave_number)) {
        $stmt = $db->prepare('SELECT * FROM leaves WHERE leave_number = :leave_number ORDER BY created_at DESC');
        $stmt->bindParam(':leave_number', $leave_number);
    } else {
        $stmt = $db->prepare('SELECT * FROM leaves WHERE id_number = :id_number ORDER BY created_at DESC');
        $stmt->bindParam(':id_number', $id_number);
    }
    
    $stmt->execute();
    $leaves = $stmt->fetchAll();
    
    if (count($leaves) > 0) {
        echo json_encode(['success' => true, 'data' => $leaves, 'count' => count($leaves)], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'No leaves found'], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
