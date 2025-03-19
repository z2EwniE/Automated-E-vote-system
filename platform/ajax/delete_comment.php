<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$comment_id = $data['comment_id'] ?? null;

if (!$comment_id) {
    echo json_encode(['success' => false, 'message' => 'Comment ID is required']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    // First check if the comment belongs to the current user
    $check_query = "SELECT student_id FROM platform_comments WHERE comment_id = :comment_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([':comment_id' => $comment_id]);
    $comment = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment || $comment['student_id'] != $_SESSION['student_id']) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized to delete this comment']);
        exit;
    }

    // Delete the comment
    $delete_query = "DELETE FROM platform_comments WHERE comment_id = :comment_id AND student_id = :student_id";
    $delete_stmt = $db->prepare($delete_query);
    $delete_stmt->execute([
        ':comment_id' => $comment_id,
        ':student_id' => $_SESSION['student_id']
    ]);

    if ($delete_stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Comment deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete comment']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}