<?php
require_once '../../config/init.php';

// Check if user is logged in and has Super Admin role
$user = new User();
$userData = $user->getUserData($_SESSION['uid'] ?? 0);
if (!isset($_SESSION['uid']) || $userData['role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get':
            $userId = $_POST['user_id'] ?? 0;
            try {
                $stmt = $db->prepare("SELECT user_id as id, username, email, role, status FROM users WHERE user_id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    echo json_encode(['success' => true, 'admin' => $user]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'User not found']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
            break;
            
        case 'update':
            $userId = $_POST['user_id'] ?? 0;
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? '';
            $status = $_POST['status'] ?? 1;
            
            try {
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, role = ?, status = ? WHERE user_id = ?");
                $result = $stmt->execute([$username, $email, $role, $status, $userId]);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update user']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
            break;
            
        case 'delete':
            $userId = $_POST['user_id'] ?? 0;
            
            // Check if current user has permission to delete users
            if (!in_array($userData['role'], ['super_admin'])) {
                echo json_encode(['success' => false, 'message' => 'You do not have permission to delete users']);
                break;
            }

            try {
                // First check if the user to be deleted is an admin or super_admin
                $stmt = $db->prepare("SELECT role FROM users WHERE user_id = ?");
                $stmt->execute([$userId]);
                $targetUser = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($targetUser && in_array($targetUser['role'], ['super_admin'])) {
                    echo json_encode(['success' => false, 'message' => 'Cannot delete super admin users']);
                    break;
                }

                $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
                $result = $stmt->execute([$userId]);
                
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
} 