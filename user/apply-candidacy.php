<?php
session_start();

include_once __DIR__ . '/db.php';




// Redirect to login page if not logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php'); 
    exit();
} else {
    $id = $_SESSION['student_id'];
}

if(isCandidate()) {
    header('Location: index.php'); 
    exit();
}


$student = fetchStudent($id);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $student_id = $_SESSION['id'];
    $department = trim($_POST['department']);
    $position_id = $_POST['position'];
    $partylist_id = $_POST['partylist'];
    
    // Validate inputs
    if (empty($department) || empty($position_id) || empty($partylist_id)) {
        echo "All fields are required.";
        exit();
    }

    // Handle file upload
    $target_dir = "uploads/profile_photos/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["profile_photo"]["name"]);
    $target_file = $target_dir . uniqid() . "_" . $file_name;
    $upload_ok = true;

    // Check file size (limit: 2MB)
    if ($_FILES["profile_photo"]["size"] > 10 * 1024 * 1024) {
        echo "File size exceeds 2MB.";
        $upload_ok = false;
    }

    // Allow certain file formats
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        $upload_ok = false;
    }

    // Upload file
    if ($upload_ok && move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
        try {
            // Prepare the SQL query for inserting a new candidate record
            $stmt = $conn->prepare("
                INSERT INTO candidates (
                    student_id, 
                    department, 
                    candidate_position, 
                    partylist_id, 
                    candidate_image_path
                ) VALUES (
                    :student_id, 
                    :department, 
                    :position_id, 
                    :partylist_id, 
                    :profile_photo
                )
            ");
        
         
            // Execute the query with parameter bindings
            $stmt->execute([
                ':student_id' => $student_id,
                ':department' => $department,
                ':position_id' => $position_id,
                ':partylist_id' => $partylist_id,
                ':profile_photo' => $target_file,
            ]);
        
            // Redirect to a confirmation page or reload the form
            header('Location: index.php?success=1'); // Optionally append success query parameter
            exit();
        } catch (PDOException $e) {
            // Log the error message and display a user-friendly error
            echo ("Database Error: " . $e->getMessage()); // Log the error for debugging
        }        
    } else {
        echo "Error uploading the file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords" content="e-vote, voting system, secure voting, elections">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/professional-icon.png" />
    <title>E-Vote System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="css/light.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <script>
    </script>
</head>

<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">
        <div class="main">
            <?php include_once 'includes/navbar.php'; ?>

            <main class="content">
                <div class="container">
                    <h2 class="mb-4">Apply for Candidacy</h2>

                  

                    <form action="apply-candidacy.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="department" id="department" value="<?= $student['department'] ?>">
                        <?php
                        include_once __DIR__ . '/db.php'; 

                        $stmt = $conn->prepare("SELECT * FROM `positions`");
                        $stmt->execute();
                        $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        $stmt = $conn->prepare("SELECT * FROM `partylists`");
                        $stmt->execute();
                        $partylists = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <!-- Dropdown form field for selecting a position -->
                        <div class="mb-3">
                            <label for="position" class="form-label">Position Applying For</label>
                            <select class="form-select" id="position" name="position" required>
                                <option value="">Select Position</option>
                                <?php foreach ($positions as $position): ?>
                                <option value="<?php echo $position['position_id']; ?>">
                                    <?php echo htmlspecialchars($position['position_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label for="partylist" class="form-label">Partylist</label>
                            <select class="form-select" id="partylist" name="partylist" required>
                                <option value="">Select Partylist</option>
                                <?php foreach ($partylists as $partylist): ?>
                                <option value="<?php echo $partylist['partylist_id']; ?>">
                                    <?php echo htmlspecialchars($partylist['partylist_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="profile_photo" class="form-label">Profile Photo</label>
                            <input type="file" class="form-control" id="profile_photo" name="profile_photo"
                                accept="image/*" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </form>
                </div>
            </main>


            <footer class="footer">
                <div class="container-fluid">
                    <div class="text-muted">
                        <div class="text-center">
                            <a class="text-muted"><strong>ISPSC-Tagudin Campus</strong></a> &copy;
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="js/app.js"></script>



</body>

</html>