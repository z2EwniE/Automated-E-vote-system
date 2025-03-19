<?php
require_once '../config/config.php';
require_once '../includes/auth_validate.php';

if (isset($_GET['id'])) {
    $election_id = $_GET['id'];
    
    $stmt = $db->prepare("DELETE FROM election_settings WHERE id = ?");
    $stmt->execute([$election_id]);
    
    $_SESSION['success'] = "Election period deleted successfully";
}

header('Location: election_settings.php');
exit(); 