<?php
require_once '../config/config.php';
require_once '../includes/auth_validate.php';

if (isset($_GET['id'])) {
    $election_id = $_GET['id'];
    
    // First, get the current status
    $stmt = $db->prepare("SELECT is_active FROM election_settings WHERE id = ?");
    $stmt->execute([$election_id]);
    $election = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($election) {
        // If we're activating this election, deactivate all others first
        if (!$election['is_active']) {
            $db->query("UPDATE election_settings SET is_active = 0");
        }
        
        // Toggle the status
        $new_status = $election['is_active'] ? 0 : 1;
        $stmt = $db->prepare("UPDATE election_settings SET is_active = ? WHERE id = ?");
        $stmt->execute([$new_status, $election_id]);
        
        $_SESSION['success'] = "Election status updated successfully";
    }
}

header('Location: election_settings.php');
exit(); 