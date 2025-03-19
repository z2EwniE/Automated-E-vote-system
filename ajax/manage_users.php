<?php
require_once '../config/init.php';

// Check if user is logged in and has Super Admin role
$user = new User();
$userData = $user->getUserData($_SESSION['uid'] ?? 0);
if (!isset($_SESSION['uid']) || $userData['role'] !== 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $register = new Register();
        $register->userRegister(
            $_POST['username'],
            $_POST['email'],
            $_POST['password'],
            $_POST['confirm_password'],
            0, // Skip captcha for admin-created accounts
            $_POST['token']
        );
        break;

    case 'get':
        $userId = $_POST['user_id'] ?? 0;
        $userData = $user->getUserData($userId);
        if ($userData) {
            echo json_encode([
                'success' => true,
                'user' => $userData
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);
        }
        break;

    case 'update':
        $userId = $_POST['user_id'] ?? 0;
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $role = $_POST['role'] ?? '';
        $status = $_POST['status'] ?? '';

        try {
            $sql = "UPDATE users SET username = :username, email = :email, role = :role, status = :status 
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':role' => $role,
                ':status' => $status,
                ':id' => $userId
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ]);
        }
        break;

    case 'delete':
        $userId = $_POST['user_id'] ?? 0;
        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $userId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error deleting user: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
} 