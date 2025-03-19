<?php
include_once '../../config/init.php';

// Add error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method";
    exit;
}

if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo "Missing required fields";
    exit;
}

$id = intval($_POST['id']);
$newStatus = $_POST['status'] === 'true' ? 1 : 0;

try {
    // Validate election exists
    $checkStmt = $db->prepare("SELECT COUNT(*) FROM election_settings WHERE id = ?");
    $checkStmt->execute([$id]);
    if ($checkStmt->fetchColumn() == 0) {
        echo "error: Election not found";
        exit;
    }

    $db->beginTransaction();
    
    // If activating this election, deactivate all others first
    if ($newStatus === 1) {
        $stmt = $db->prepare("UPDATE election_settings SET is_active = 0 WHERE id != ?");
        $stmt->execute([$id]);
    }
    
    // Update the status of the selected election
    $stmt = $db->prepare("UPDATE election_settings 
                         SET is_active = ?, 
                             updated_at = CURRENT_TIMESTAMP 
                         WHERE id = ?");
    $result = $stmt->execute([$newStatus, $id]);
    
    if ($result) {
        $db->commit();
        echo "success";
    } else {
        $db->rollBack();
        echo "Failed to update election status";
    }
} catch (PDOException $e) {
    $db->rollBack();
    error_log("Database error: " . $e->getMessage());
    echo "Database error: " . $e->getMessage();
} 