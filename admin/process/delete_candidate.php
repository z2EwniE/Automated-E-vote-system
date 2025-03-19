<?php
include_once __DIR__ . "/../../config/init.php";

header('Content-Type: application/json');

try {
    if (!isset($_POST['student_id'])) {
        throw new Exception('Student ID is required');
    }

    $student_id = trim($_POST['student_id']);

    // First get the candidate record to ensure it exists
    $stmt = $db->prepare("SELECT candidate_id FROM candidates 
                         INNER JOIN students ON students.id = candidates.candidate_id 
                         WHERE students.student_id = ?");
    $stmt->execute([$student_id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Candidate not found');
    }

    // Delete the candidate
    $stmt = $db->prepare("DELETE candidates FROM candidates 
                         INNER JOIN students ON students.id = candidates.candidate_id 
                         WHERE students.student_id = ?");
    $stmt->execute([$student_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Candidate deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete candidate');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 