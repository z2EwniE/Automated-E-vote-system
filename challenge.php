<?php

include_once 'config/init.php';


if(!Session::checkSession('tfaChallenge')){
    header('Location: index.php');
}

if ($login->isLoggedIn()) {
    header('Location: index.php');
    die();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | <?= APP_NAME ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/my-login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <!--    Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/fontawesome.css"/>
</head>

<body class="my-login-page">
<section class="h-100">
    <div class="container h-100">
        <div class="row justify-content-md-center h-100">
            <div class="card-wrapper">
                <div class="brand">
                    <img src="assets/images/logo/phpLoginRegisterSystem.png" alt="logo">
                </div>
                <div class="card fat">
                    <div class="card-body">
                        <h4 class="card-title">Login to <?= APP_NAME ?> </h4>
                        <form method="POST" id="tfa_form" >
                            <input type="hidden" name="token" id="token" value="<?= htmlentities(CSRF::generate('tfa_form')); ?>">

                            <div class="form-group">
                                <p>You have turned on Two Factor Authentication</p>
                            </div>

                            <div class="form-group">
                                <label for="password">Code
                                    <a href="#" data-toggle="modal" data-target="#forgotPasswordModal" class="float-right">
                                        Forgot Password?
                                    </a>
                                </label>
                                <input id="code" type="number" class="form-control" name="code">    
                            </div>


                            <div class="form-group m-0">
                                <button type="submit" id="challenge_btn" class="btn btn-primary btn-block">
                                     Submit Code
                                </button>
                            </div>
                            <div class="mt-4 text-center">
                                Not your account? <a href="login.php?ref=1">Login</a>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="footer">
                    Copyright &copy; <?= date("Y") ?> &mdash; <?= APP_NAME ?>
                </div>
            </div>
        </div>
    </div>
</section>


        <!-- Forgot Password Modal -->
        <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Forgot my Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body m-3">
                        <p class="mb-1">To continue, please enter your email for recovery.</p>
                        <div class="form-group">
                            <label for="forgotPasswordEmail">Email</label>
                            <input type="email" id="forgotPasswordEmail" class="form-control" placeholder="Enter your email">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="forgotPasswordBtn" data-bs-dismiss="modal" class="btn btn-primary">Reset Password</button>
                    </div>
                </div>
            </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sha512.min.js"></script>
<script src="assets/js/login.js"></script>
</body>
</html>
