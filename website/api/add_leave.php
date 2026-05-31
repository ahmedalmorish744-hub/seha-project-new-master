<?php
require_once 'config.php';

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
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
        echo json_encode(['success' => false, 'message' => 'Leave number and ID number are required']);
        exit();
    }
    
    $stmt = $db->prepare('SELECT id FROM leaves WHERE leave_number = :leave_number');
    $stmt->bindParam(':leave_number', $leave_number);
    $stmt->execute();
    
    if ($stmt->fetch()) {
        $stmt = $db->prepare('UPDATE leaves SET name = :name, report_date = :report_date, entry_date = :entry_date, exit_date = :exit_date, doctor = :doctor, job_title = :job_title WHERE leave_number = :leave_number');
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':report_date', $report_date);
        $stmt->bindParam(':entry_date', $entry_date);
        $stmt->bindParam(':exit_date', $exit_date);
        $stmt->bindParam(':doctor', $doctor);
        $stmt->bindParam(':job_title', $job_title);
        $stmt->bindParam(':leave_number', $leave_number);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Leave updated successfully', 'data' => ['leave_number' => $leave_number]]);
    } else {
        $stmt = $db->prepare('INSERT INTO leaves (leave_number, id_number, name, report_date, entry_date, exit_date, doctor, job_title) VALUES (:leave_number, :id_number, :name, :report_date, :entry_date, :exit_date, :doctor, :job_title)');
        $stmt->bindParam(':leave_number', $leave_number);
        $stmt->bindParam(':id_number', $id_number);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':report_date', $report_date);
        $stmt->bindParam(':entry_date', $entry_date);
        $stmt->bindParam(':exit_date', $exit_date);
        $stmt->bindParam(':doctor', $doctor);
        $stmt->bindParam(':job_title', $job_title);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Leave saved successfully', 'data' => ['leave_number' => $leave_number]]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
