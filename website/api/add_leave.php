<?php
require_once 'config.php';

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'بيانات غير صالحة']);
        exit();
    }
    
    $leave_number = $data['leaveNumber'] ?? '';
    $id_number = $data['idNumber'] ?? '';
    $name = $data['name'] ?? '';
    $report_date = $data['reportDate'] ?? '';
    $entry_date = $data['entryDate'] ?? '';
    $exit_date = $data['exitDate'] ?? '';
    $doctor = $data['doctor'] ?? '';
    $job_title = $data['jobTitle'] ?? '';
    
    if (empty($leave_number) || empty($id_number)) {
        echo json_encode(['success' => false, 'message' => 'رمز الخدمة ورقم الهوية مطلوبان']);
        exit();
    }
    
    // Check if leave already exists
    $stmt = $db->prepare('SELECT id FROM leaves WHERE leave_number = :leave_number');
    $stmt->bindValue(':leave_number', $leave_number, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    if ($result->fetchArray()) {
        // Update existing record
        $stmt = $db->prepare('UPDATE leaves SET name = :name, report_date = :report_date, entry_date = :entry_date, exit_date = :exit_date, doctor = :doctor, job_title = :job_title WHERE leave_number = :leave_number');
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':report_date', $report_date, SQLITE3_TEXT);
        $stmt->bindValue(':entry_date', $entry_date, SQLITE3_TEXT);
        $stmt->bindValue(':exit_date', $exit_date, SQLITE3_TEXT);
        $stmt->bindValue(':doctor', $doctor, SQLITE3_TEXT);
        $stmt->bindValue(':job_title', $job_title, SQLITE3_TEXT);
        $stmt->bindValue(':leave_number', $leave_number, SQLITE3_TEXT);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'تم تحديث بيانات الإجازة بنجاح', 'data' => ['leave_number' => $leave_number]]);
    } else {
        // Insert new record
        $stmt = $db->prepare('INSERT INTO leaves (leave_number, id_number, name, report_date, entry_date, exit_date, doctor, job_title) VALUES (:leave_number, :id_number, :name, :report_date, :entry_date, :exit_date, :doctor, :job_title)');
        $stmt->bindValue(':leave_number', $leave_number, SQLITE3_TEXT);
        $stmt->bindValue(':id_number', $id_number, SQLITE3_TEXT);
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':report_date', $report_date, SQLITE3_TEXT);
        $stmt->bindValue(':entry_date', $entry_date, SQLITE3_TEXT);
        $stmt->bindValue(':exit_date', $exit_date, SQLITE3_TEXT);
        $stmt->bindValue(':doctor', $doctor, SQLITE3_TEXT);
        $stmt->bindValue(':job_title', $job_title, SQLITE3_TEXT);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'تم حفظ بيانات الإجازة بنجاح', 'data' => ['leave_number' => $leave_number]]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ: ' . $e->getMessage()]);
}
?>
