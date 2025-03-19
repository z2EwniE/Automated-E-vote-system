<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .thank-you-container {
            max-width: 600px;
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .thank-you-title {
            font-size: 2rem;
            font-weight: 700;
            color: #28a745;
            margin-bottom: 20px;
        }

        .thank-you-message {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 30px;
        }

        .thank-you-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .footer {
            margin-top: 20px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="thank-you-container">
        <div class="thank-you-icon">
            <i class="bi bi-check-circle-fill"></i> <!-- Bootstrap Icon for success -->
        </div>
        <h1 class="thank-you-title">Thank You for Voting!</h1>
        <p class="thank-you-message">Your vote has been successfully submitted. We appreciate your participation.</p>
        <a href="login.php" class="btn btn-primary">Logout</a>
        <div class="footer">
            <p>© 2024 E-vote. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
