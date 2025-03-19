<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$platform_id = $data['platform_id'] ?? null;
$content = $data['content'] ?? null;

if (!$platform_id || !$content) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    // Insert new comment
    $comment_query = "INSERT INTO platform_comments (platform_id, student_id, content) 
                     VALUES (:platform_id, :student_id, :content)";
    $comment_stmt = $db->prepare($comment_query);
    $comment_stmt->execute([
        ':platform_id' => $platform_id,
        ':student_id' => $_SESSION['student_id'],
        ':content' => $content
    ]);
    
    // Get student name for response
    $student_query = "SELECT first_name, last_name FROM students WHERE id = :student_id";
    $student_stmt = $db->prepare($student_query);
    $student_stmt->execute([':student_id' => $_SESSION['student_id']]);
    $student = $student_stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'comment' => [
            'student_name' => $student['first_name'] . ' ' . $student['last_name'],
            'content' => $content,
            'created_at' => date('M j, Y g:i A')
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
} 