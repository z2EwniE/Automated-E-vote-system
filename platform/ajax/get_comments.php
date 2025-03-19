<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['platform_id'])) {
    echo json_encode(['success' => false, 'message' => 'Platform ID is required']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Updated query to get student and candidate information
    $query = "SELECT 
                pc.comment_id,
                pc.content as comment_text,
                pc.created_at,
                CASE 
                    WHEN s.id IS NOT NULL THEN CONCAT(s.first_name, ' ', s.last_name)
                    WHEN c.candidate_id IS NOT NULL THEN c.candidate_name
                    ELSE 'Unknown User'
                END as commenter_name,
                CASE 
                    WHEN c.candidate_id IS NOT NULL THEN c.candidate_image_path
                    ELSE NULL
                END as commenter_image
              FROM platform_comments pc
              LEFT JOIN students s ON pc.student_id = s.id
              LEFT JOIN candidates c ON pc.student_id = c.candidate_id
              WHERE pc.platform_id = :platform_id
              ORDER BY pc.created_at DESC
              LIMIT 50";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':platform_id' => $_GET['platform_id']]);
    
    $comments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $comments[] = [
            'comment_id' => $row['comment_id'],
            'voter_name' => htmlspecialchars($row['commenter_name']),
            'comment_text' => htmlspecialchars($row['comment_text']),
            'voter_image' => $row['commenter_image'] ? htmlspecialchars($row['commenter_image']) : 'assets/images/default-avatar.png',
            'created_at' => date('Y-m-d H:i:s', strtotime($row['created_at']))
        ];
    }
    
    // Log the response we're sending
    error_log("Sending response: " . json_encode([
        'success' => true,
        'comments' => $comments
    ]));
    
    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_comments.php: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load comments'
    ]);
} catch (Exception $e) {
    error_log("General error in get_comments.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
} 