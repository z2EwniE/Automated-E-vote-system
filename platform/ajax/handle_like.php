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

if (!$platform_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid platform ID']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

try {
    // Check if already liked
    $check_query = "SELECT * FROM platform_likes WHERE platform_id = :platform_id AND student_id = :student_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([
        ':platform_id' => $platform_id,
        ':student_id' => $_SESSION['student_id']
    ]);
    
    if ($check_stmt->rowCount() > 0) {
        // Unlike - remove the like
        $unlike_query = "DELETE FROM platform_likes WHERE platform_id = :platform_id AND student_id = :student_id";
        $unlike_stmt = $db->prepare($unlike_query);
        $unlike_stmt->execute([
            ':platform_id' => $platform_id,
            ':student_id' => $_SESSION['student_id']
        ]);
        $is_liked = false;
    } else {
        // Like - add new like
        $like_query = "INSERT INTO platform_likes (platform_id, student_id) VALUES (:platform_id, :student_id)";
        $like_stmt = $db->prepare($like_query);
        $like_stmt->execute([
            ':platform_id' => $platform_id,
            ':student_id' => $_SESSION['student_id']
        ]);
        $is_liked = true;
    }
    
    // Get updated like count
    $count_query = "SELECT COUNT(*) as count FROM platform_likes WHERE platform_id = :platform_id";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute([':platform_id' => $platform_id]);
    $like_count = $count_stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo json_encode([
        'success' => true,
        'is_liked' => $is_liked,
        'like_count' => $like_count
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
} 