<?php
include_once __DIR__ . "/../../config/init.php";

// Add error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (empty($_POST['election_title']) || empty($_POST['election_start']) || empty($_POST['election_end'])) {
        echo "error: Missing required fields";
        exit;
    }

    // Validate dates
    $startDate = strtotime($_POST['election_start']);
    $endDate = strtotime($_POST['election_end']);
    
    if ($startDate >= $endDate) {
        echo "error: End date must be after start date";
        exit;
    }

    try {
        $db->beginTransaction();

        // If this election will be active, deactivate all other elections first
        if (isset($_POST['is_active']) && $_POST['is_active'] === 'on') {
            $stmt = $db->prepare("UPDATE election_settings SET is_active = 0");
            $stmt->execute();
        }

        // Insert new election period
        $stmt = $db->prepare("INSERT INTO election_settings (election_title, election_start, election_end, is_active) VALUES (?, ?, ?, ?)");
        $isActive = isset($_POST['is_active']) && $_POST['is_active'] === 'on' ? 1 : 0;
        
        $stmt->execute([
            $_POST['election_title'],
            $_POST['election_start'],
            $_POST['election_end'],
            $isActive
        ]);

        $db->commit();
        echo "success";
    } catch (PDOException $e) {
        $db->rollBack();
        error_log("Database error: " . $e->getMessage());
        echo "error: " . $e->getMessage();
    }
} else {
    echo "error: Invalid request method";
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
} 