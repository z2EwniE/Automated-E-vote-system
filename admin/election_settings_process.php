<?php
require_once '../config/config.php';
require_once '../includes/auth_validate.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_title = $_POST['election_title'];
    $election_start = $_POST['election_start'];
    $election_end = $_POST['election_end'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    try {
        // If activating this election, deactivate all others first
        if ($is_active) {
            $db->query("UPDATE election_settings SET is_active = 0");
        }

        $stmt = $db->prepare("INSERT INTO election_settings (election_title, election_start, election_end, is_active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$election_title, $election_start, $election_end, $is_active]);

        $_SESSION['success'] = "Election period created successfully";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error creating election period: " . $e->getMessage();
    }
}

header('Location: election_settings.php');
exit(); 