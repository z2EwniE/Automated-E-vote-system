<?php
require_once '../config/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $register = new Register();
    
    // Generate new captcha for next attempt
    $register->generateCaptcha();
    
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $captcha = $_POST['captcha'] ?? '';
    $token = $_POST['token'] ?? '';

    $register->userRegister(
        $username,
        $email,
        $password,
        $confirm_password,
        $captcha,
        $token
    );
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
} 