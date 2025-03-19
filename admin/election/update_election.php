<?php
include_once '../../config/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method";
    exit;
}

if (!isset($_POST['election_id']) || !isset($_POST['election_title']) || 
    !isset($_POST['election_start']) || !isset($_POST['election_end'])) {
    echo "Missing required fields";
    exit;
}

try {
    $stmt = $db->prepare("UPDATE election_settings 
                         SET election_title = ?, 
                             election_start = ?, 
                             election_end = ?,
                             updated_at = CURRENT_TIMESTAMP
                         WHERE id = ?");
                         
    $result = $stmt->execute([
        $_POST['election_title'],
        $_POST['election_start'],
        $_POST['election_end'],
        $_POST['election_id']
    ]);
    
    if ($result) {
        echo "success";
    } else {
        echo "Failed to update election";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} 