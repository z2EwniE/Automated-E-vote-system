<?php
include_once '../../config/init.php';

if (!isset($_GET['id'])) {
    echo "No election ID provided";
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $db->prepare("SELECT * FROM election_settings WHERE id = ?");
    $stmt->execute([$id]);
    $election = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($election) {
        echo json_encode($election);
    } else {
        echo "Election not found";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} 