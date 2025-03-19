•	<?php

include_once __DIR__ . "/../config/init.php";

if (isset($_GET["ref"])) {
    Session::unsetSession("tfaChallenge");
    Session::unsetSession("uid");
}

if (isset($_GET["ref_"])) {
    Cookie::clear("remember_me");
    Cookie::clear("uid");
}

if ($login->isLoggedIn()) {
    header("Location: index.php");
    die();
}

if ($login->isRememberSet()) {
    $user = new User();
    $user_id = Cookie::get("uid");
    $uid = Others::decryptData($user_id, ENCRYPTION_KEY);
    $row = $user->getUserData($uid);

    if(empty($row)){
        Cookie::clear("remember_me");
        Cookie::clear("uid");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - E-Vote System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
        }

        .left-section {
            background-color: #4263EB;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            width: 50%;
        }

        .right-section {
            width: 50%;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome-text {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .welcome-subtext {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .login-form {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1a1a1a;
        }

        .quick-login-box {
            background-color: #F3F4FF;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .quick-login-icon {
            background-color: #4263EB;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quick-login-text {
            flex: 1;
        }

        .quick-login-text h6 {
            margin: 0;
            font-weight: 500;
            color: #1a1a1a;
        }

        .quick-login-text p {
            margin: 0;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .form-control:focus {
            border-color: #4263EB;
            box-shadow: 0 0 0 2px rgba(66, 99, 235, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background-color: #4263EB;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 1rem;
        }

        .btn-login:hover {
            background-color: #3651c7;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #6b7280;
        }

        .login-footer a {
            color: #4263EB;
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .welcome-image {
            max-width: 280px;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .left-section, .right-section {
                width: 100%;
                padding: 20px;
            }
            .left-section {
                min-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="left-section">
            <img src="../assets/images/login-illustration.png" alt="Welcome" class="welcome-image">
            <h1 class="welcome-text">Welcome Back!</h1>
            <p class="welcome-subtext">Login to access your admin dashboard and manage the voting system.</p>
        </div>
        
        <div class="right-section">
            <div class="login-form">
                <h2 class="login-title">Login to E-Vote Admin</h2>
                
                <?php if (!$login->isRememberSet()): ?>
                <form method="POST" id="login_form">
                    <input type="hidden" name="token" id="token" value="<?= htmlentities(CSRF::generate("login_form")) ?>">
                    
                    <div class="quick-login-box">
                        <div class="quick-login-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="quick-login-text">
                            <h6>Secure Admin Access</h6>
                            <p>Login with your admin credentials</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <input type="username" class="form-control" name="username" id="username" placeholder="Username" required>
                    </div>

                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required data-eye>
                    </div>

                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" name="remember_me" id="remember_me">
                            <label class="form-check-label" for="remember_me">
                                Keep me logged in
                            </label>
                        </div>
                        <a href="#" data-toggle="modal" data-target="#forgotPasswordModal" class="text-decoration-none">Forgot Password?</a>
                    </div>

                    <button type="submit" id="login_button" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </button>
                </form>
                <?php else: ?>
                <form method="POST" id="login_form">
                    <h4 class="text-center mb-4">Login as <?= htmlentities($row["username"]) ?></h4>
                    <input type="hidden" name="token" id="token" value="<?= htmlentities(CSRF::generate("login_form")) ?>">
                    <input id="username" type="hidden" class="form-control" name="username" value="<?= $row["username"] ?>" autofocus>
                    
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required data-eye>
                    </div>

                    <button type="submit" id="login_button" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </button>

                    <div class="login-footer">
                        <a href="login.php?ref_">Not your account? Switch Account</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Enter your username to reset your password</p>
                    <div class="mb-3">
                        <input type="username" id="forgotPasswordusername" class="form-control" placeholder="Enter your username">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="forgotPasswordBtn" class="btn btn-primary">
                        Reset Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="../assets/js/sha512.min.js"></script>
    <script src="../assets/js/login.js"></script>
</body>
</html>
