<?php
require_once '../../config/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $stmt = $db->prepare("DELETE FROM election_settings WHERE id = ?");
        $stmt->execute([$_POST['id']]);
        echo "success";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} 