<?php

include_once __DIR__ . "/../../config/init.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $partylistId = trim($_POST['partylist_id']);
    $partylistName = trim($_POST['partylist_name']);
    $department = trim($_POST['department']);

    if ($partylistId && $partylistName && $department) {
        try {
            $stmt = $db->prepare("UPDATE partylists SET partylist_name = :partylist_name, department = :department WHERE partylist_id = :partylist_id");
            $stmt->bindParam(':partylist_id', $partylistId, PDO::PARAM_INT);
            $stmt->bindParam(':partylist_name', $partylistName, PDO::PARAM_STR);
            $stmt->bindParam(':department', $department, PDO::PARAM_INT);
            $stmt->execute();
            echo "success";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "All fields are required.";
    }
}
