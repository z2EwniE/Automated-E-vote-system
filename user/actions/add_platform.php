<?php
session_start();
include_once '../db.php';

if (isCandidate()) {
    $candidateId = $_SESSION['id'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if (isset($_POST['action'])) {
    $platform_title = trim($_POST['platform_title']);
    $platform_content = trim($_POST['platform_content']);

    if (!empty($platform_title) && !empty($platform_content)) {
        $sql = "INSERT INTO platforms (candidate_id, platform_title, platform_content) 
                VALUES (:cid, :pt, :pc)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $candidateId, PDO::PARAM_INT);
        $stmt->bindParam(':pt', $platform_title, PDO::PARAM_STR);
        $stmt->bindParam(':pc', $platform_content, PDO::PARAM_STR);

        try {
            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'Platform added successfully']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in both the platform title and content.']);
    }
}
?>
