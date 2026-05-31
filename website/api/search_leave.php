<?php
require_once 'config.php';

try {
    $leave_number = $_GET['leave_number'] ?? '';
    $id_number = $_GET['id_number'] ?? '';
    
    if (empty($leave_number) && empty($id_number)) {
        echo json_encode(['success' => false, 'message' => 'يرجى إدخال رمز الخدمة أو رقم الهوية']);
        exit();
    }
    
    if (!empty($leave_number) && !empty($id_number)) {
        // Search by both
        $stmt = $db->prepare('SELECT * FROM leaves WHERE leave_number = :leave_number AND id_number = :id_number ORDER BY created_at DESC');
        $stmt->bindValue(':leave_number', $leave_number, SQLITE3_TEXT);
        $stmt->bindValue(':id_number', $id_number, SQLITE3_TEXT);
    } elseif (!empty($leave_number)) {
        // Search by leave number only
        $stmt = $db->prepare('SELECT * FROM leaves WHERE leave_number = :leave_number ORDER BY created_at DESC');
        $stmt->bindValue(':leave_number', $leave_number, SQLITE3_TEXT);
    } else {
        // Search by ID number only
        $stmt = $db->prepare('SELECT * FROM leaves WHERE id_number = :id_number ORDER BY created_at DESC');
        $stmt->bindValue(':id_number', $id_number, SQLITE3_TEXT);
    }
    
    $result = $stmt->execute();
    $leaves = [];
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $leaves[] = $row;
    }
    
    if (count($leaves) > 0) {
        echo json_encode(['success' => true, 'data' => $leaves, 'count' => count($leaves)]);
    } else {
        echo json_encode(['success' => false, 'message' => 'لم يتم العثور على إجازات بهذه البيانات']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}
?>
