<?php

include_once __DIR__ . "/../config/init.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="qoshima">
    <meta name="keywords" content=" Admin, dashboard, responsive, css, sass, html, theme, front-end">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />
    <title>E-Vote System</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Choose your preferred color scheme -->
    <link href="css/light.css" rel="stylesheet">

    <style>


        /* Toggle switch styling */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 50px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            border-radius: 50px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
        }

        input:checked + .slider {
            background-color: #5e72e4;
        }

        input:checked + .slider:before {
            transform: translateX(22px);
        }

  

    </style>
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">

        <?php
            include_once 'includes/sidebar.php';
        ?>

        <div class="main">

            <?php
                include_once 'includes/navbar.php';
            ?>

            <main class="content">

                <div class="container-fluid p-0">
                    <div class="mb-3">
                        <h1 class="h3 d-inline align-middle">System Settings</h1>
                    </div>

                    <div class="row">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">System Settings</div>

                                <form>
                                    <div class="mb-3">
                                        <label for="is_voting_active">Vote Casting</label>
                                        <label class="switch">
                                            <input type="checkbox" id="is_voting_active" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>

                                    <div class="mb-3">
                                        <label for="voting_start_time">Voting Start Time</label>
                                        <input type="time" class="form-control" name="voting_start_time" id="voting_start_time">
                                    </div>

                                    <div class="mb-3">
                                        <label for="voting_end_time">Voting End Time</label>
                                        <input type="time" class="form-control" name="voting_end_time" id="voting_end_time">
                                    </div>

                                    <div class="mb-3">
                                        <label for="school_year">School Year</label>
                                        <select name="school_year" class="form-control" id="school_year">
                                            <option value="1">2023-2024</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Save Settings</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </main>

            <footer class="footer">
                <div>
                    <p class="mb-0">
                        <a target="_blank" class="text-muted"><strong>eVote System</strong></a> &copy; ISPSC - Tagudin Campus
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script src="js/app.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
</body>

</html>
