<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['candidate_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if (!isset($_POST['platform_id']) || !isset($_POST['comment'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO platform_comments (platform_id, student_id, content, created_at) 
              VALUES (:platform_id, :student_id, :content, NOW())";
    
    $stmt = $db->prepare($query);
    $result = $stmt->execute([
        ':platform_id' => $_POST['platform_id'],
        ':student_id' => $_SESSION['candidate_id'],
        ':content' => $_POST['comment']
    ]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to add comment');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error adding comment'
    ]);
} 