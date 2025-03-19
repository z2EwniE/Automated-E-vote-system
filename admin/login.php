<?php

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
    <title>Admin Login | <?= APP_NAME ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link rel="stylesheet" href="svg/login-styles.css">
    <link rel="stylesheet" href="css/register.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }

        .scanner-info {
            background: rgba(79, 70, 229, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            border: 1px solid rgba(79, 70, 229, 0.2);
        }

        .scanner-icon {
            background:#0072ff;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scanner-icon svg {
            color: white;
        }

        .scanner-text h3 {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
        }

        .scanner-text p {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            color: #64748b;
        }

        .input-with-icon input {
            padding-left: 40px;
        }

        button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #0072ff;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        button svg {
            transition: transform 0.3s ease;
        }

        button:hover svg {
            transform: translateX(3px);
        }

        .register-prompt {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .register-prompt a {
            color:#0072ff;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .register-prompt a:hover {
            color: #3730a3;
            text-decoration: underline;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
          
        }

        @media (max-width: 768px) {
            .scanner-info {
                flex-direction: column;
                text-align: center;
            }
            
            .scanner-icon {
                margin: 0 auto;
            }
        }

        /* Loading Spinner */
        .spinner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            display: none; /* Hidden by default */
        }
    </style>
</head>

<body>
    <div class="main-container">
            <div class="left-section">
                <div class="left-content">
                    <img src="svg/login-not-css.svg" alt="Login illustration">
                    <h1>Welcome Admin!</h1>
                    <p>Manage and monitor the E-Voting system efficiently and securely.</p>
                    <div class="features">
                        <div class="feature-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span>Quick login Access</span>
                        </div>
                        <div class="feature-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            <span>Secure and confidential</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-section">
                <h2>Login to Admin Panel</h2>
                <div class="scanner-info">
                        <div class="scanner-icon">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 10V9C7 6.23858 9.23858 4 12 4C14.7614 4 17 6.23858 17 9V10"/>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14V17"/>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 15H16"/>
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 10H18C19.1046 10 20 10.8954 20 12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12C4 10.8954 4.89543 10 6 10Z"/>
                            </svg>
                            </div>
                        <div class="scanner-text">
                            <h3>Admin Access Required</h3>
                        <p>Please login with your admin credentials to access the control panel</p>
                    </div>
                </div>

                                                <?php if (!$login->isRememberSet()): ?>
                                                    <form method="POST" id="login_form">
                                                        <input type="hidden" name="token" id="token" value="<?= htmlentities(CSRF::generate("login_form")) ?>">
                                                        
                                                        <div class="input-with-icon mb-3">
                                                            <span class="input-icon">
                                                                <i class="fas fa-user"></i>
                                                            </span>
                                                            <input type="username" class="form-control" name="username" id="username" placeholder="Username" required>
                                                        </div>
                                                        
                                                        <div class="input-with-icon mb-3">
                                                            <span class="input-icon">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required data-eye>
                                                        </div>

                                                        <div class="mb-3 d-flex justify-content-between align-items-center">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" name="remember_me" id="remember_me">
                                                                <label class="form-check-label mb-0" for="remember_me">
                                                                    Keep me logged in
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <div class="d-grid">
                                                            <button type="submit" id="login_button" class="btn btn-primary">
                                                                <i class="fas fa-sign-in-alt"></i>
                                                                Log in
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 4l6 6-6 6M5 10h14"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" id="login_form">
                                                        <h4 class="card-title text-center">Login as <?= htmlentities($row["username"]) ?></h4>
                                                        <input type="hidden" name="token" id="token" value="<?= htmlentities(CSRF::generate("login_form")) ?>">
                                                        <input id="username" type="hidden" class="form-control" name="username" value="<?= $row["username"] ?>" autofocus>
                                                        
                                                        <div class="input-with-icon mb-3">
                                                            <span class="input-icon">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required data-eye>
                                                        </div>

                                                        <div class="d-grid">
                                                            <button type="submit" id="login_button" class="btn btn-primary">
                                                                <i class="fas fa-sign-in-alt"></i>
                                                                Log in
                                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 4l6 6-6 6M5 10h14"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </form>
                                                <?php endif; ?>

                                            

                
        </div>

     <!-- Forgot Password Modal -->
     <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Forgot Password</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-3">To reset your password, please enter your username.</p>
                            <div class="form-floating">
                                <input type="username" id="forgotPasswordusername" class="form-control" placeholder="Enter your username">
                                <label for="forgotPasswordusername">Username</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" id="forgotPasswordBtn" class="btn btn-primary">
                                <i class="fas fa-key me-2"></i> Reset Password
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
            <script src="../assets/js/register.js"></script>
       
    <!--strap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    
</body>

</html>
