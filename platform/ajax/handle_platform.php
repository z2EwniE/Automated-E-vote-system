<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['candidate_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$action = $_POST['action'] ?? '';
$platform_id = $_POST['platform_id'] ?? null;

// Verify platform belongs to candidate
$verify_query = "SELECT * FROM platforms WHERE platform_id = :platform_id AND candidate_id = :candidate_id";
$verify_stmt = $db->prepare($verify_query);
$verify_stmt->execute([
    ':platform_id' => $platform_id,
    ':candidate_id' => $_SESSION['candidate_id']
]);

if ($verify_stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    switch ($action) {
        case 'delete':
            // Delete platform media files first
            $platform = $verify_stmt->fetch(PDO::FETCH_ASSOC);
            if ($platform['image_path']) {
                $media_files = json_decode($platform['image_path'], true);
                foreach ($media_files as $file) {
                    $file_path = '../' . $file;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
            
            // Delete platform record
            $delete_query = "DELETE FROM platforms WHERE platform_id = :platform_id";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->execute([':platform_id' => $platform_id]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'update':
            try {
                $db->beginTransaction();
                
                $platform_id = $_POST['platform_id'];
                $content = $_POST['content'];
                $retained_media = json_decode($_POST['retained_media'], true) ?? [];
                
                // Handle new media uploads
                $new_media_paths = [];
                if (!empty($_FILES['new_media']['name'][0])) {
                    foreach ($_FILES['new_media']['tmp_name'] as $key => $tmp_name) {
                        if ($_FILES['new_media']['error'][$key] == 0) {
                            $upload_dir = '../uploads/platforms/';
                            if (!file_exists($upload_dir)) {
                                mkdir($upload_dir, 0777, true);
                            }
                            
                            $file_extension = pathinfo($_FILES['new_media']['name'][$key], PATHINFO_EXTENSION);
                            $new_filename = uniqid() . '_' . $_FILES['new_media']['name'][$key];
                            $upload_path = $upload_dir . $new_filename;
                            
                            if (move_uploaded_file($tmp_name, $upload_path)) {
                                $new_media_paths[] = 'uploads/platforms/' . $new_filename;
                            }
                        }
                    }
                }
                
                // Combine retained and new media paths
                $all_media_paths = array_merge($retained_media, $new_media_paths);
                
                // Update platform
                $update_query = "UPDATE platforms 
                                SET content = :content, 
                                    image_path = :image_path,
                                    updated_at = NOW() 
                                WHERE platform_id = :platform_id 
                                AND candidate_id = :candidate_id";
                
                $stmt = $db->prepare($update_query);
                $stmt->execute([
                    ':content' => $content,
                    ':image_path' => !empty($all_media_paths) ? json_encode($all_media_paths) : null,
                    ':platform_id' => $platform_id,
                    ':candidate_id' => $_SESSION['candidate_id']
                ]);
                
                $db->commit();
                echo json_encode(['success' => true]);
                
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
} 