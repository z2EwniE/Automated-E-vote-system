<?php
    include 'connect.php';
    $departments = [];
    $sql = "SELECT department_id, department_name FROM department";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
    } else {
        echo "No departments found.";
    }
    function isStudentEnrolled($student_id) {
        // Read the CSV file
        $file = fopen('enrolledstudent.csv', 'r');
        if (!$file) {
            return false;
        }

        // Skip the header row
        fgetcsv($file);

        // Search for the student ID
        while (($row = fgetcsv($file, 0, ',')) !== FALSE) {
            // Remove any quotes and trim whitespace
            $enrolledId = trim(str_replace('"', '', $row[0]));
            $enrolled = trim(str_replace('"', '', $row[6]));
            
            if ($enrolledId === $student_id && $enrolled === 'Y') {
                fclose($file);
                return true;
            }
        }

        fclose($file);
        return false;
    }
    $error_message = '';
    $success_message = '';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $student_id = $_POST['student_id'];
        
        // First check if student is enrolled
        if (!isStudentEnrolled($student_id)) {
            $error_message = 'Student ID not found in enrolled list or not currently enrolled!';
        } else {
            // Continue with existing registration logic
            $first_name = $_POST['first_name'];
            $middle_name = $_POST['middle_name'];
            $last_name = $_POST['last_name'];
            $suffix_name = $_POST['suffix_name'];
            $course = $_POST['course'];
            $year_level = $_POST['year_level'];
            $department = $_POST['department'];

            $sql = "SELECT * FROM students WHERE student_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $student_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error_message = 'Student ID already exists in the system!';
            } else {
                $sql = "INSERT INTO students (student_id, first_name, middle_name, last_name, suffix_name, course, year_level, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssi", $student_id, $first_name, $middle_name, $last_name, $suffix_name, $course, $year_level, $department);
                if ($stmt->execute()) {
                    echo "<script>window.location.href='login.php';</script>";
                } else {
                    $error_message = "Error: " . $conn->error;
                }
            }
            $stmt->close();
        }
    }
    $conn->close();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Register - E-Vote System</title>
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <link rel="stylesheet" href="svg/sign-up-styles.css">
        <link rel="stylesheet" href="css/register.css">
    </head>
    <body>
        <div class="main-container">
            <div class="left-section">
                <div class="left-content">
                    <img src="svg/sign-up-not-css.svg" alt="Registration illustration">
                    <h1>Register to Vote</h1>
                    <p>Make your voice heard! Join and participate in shaping the future of student leadership.</p>
                    <div class="features">
                        <div class="feature-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span>Quick and easy registration</span>
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
                <h2>Create Account</h2>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="input-group-half">
                        <div class="input-group">
                            <label for="student_id">Student ID</label>
                            <input type="text" id="student_id" name="student_id" placeholder="Use valid Student ID" required>
                        </div>
                        <div class="input-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                    </div>

                    <div class="input-group-half">
                        <div class="input-group">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" id="middle_name" name="middle_name">
                        </div>
                        <div class="input-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>

                    <div class="input-group-half">
                        <div class="input-group">
                            <label for="suffix_name">Suffix Name</label>
                            <input type="text" id="suffix_name" name="suffix_name" placeholder="N/A if none">
                        </div>
                        <div class="input-group">
                            <label for="year_level">Year Level</label>
                            <select id="year_level" name="year_level" required>
                                <option value="">Select Year Level</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group-half">
                        <div class="input-group">
                            <label for="department">Department</label>
                            <select id="department" name="department" required>
                                <option value="">Select Department</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="course">Course</label>
                            <select id="course" name="course" required>
                                <option value="">Select Course</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit">Register</button>
                </form>
                <p>Already have an account? <a href="login.php">Log in</a></p>
            </div>
        </div>

        <!-- Keep the existing JavaScript code -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        
        <script>
            $(document).ready(function () {
                function fetchBarcode() {
                    // Fetch the scan URL
                    fetch('http://192.168.0.100:5000/scan')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                $('#student_id').val(data.barcode);
                            } else {
                                $('#message').text('Failed to retrieve barcode.').show();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            $('#message').text('Failed to start login process.').show();
                        });
                }
        
                // Automatically fetch barcode every 2 seconds
                setInterval(fetchBarcode, 2000);
            });
            </script>

            <script>
                    $(document).ready(function() {
                        // Fetch departments on page load
                        $.ajax({
                            url: 'fetch_department.php',
                            method: 'GET',
                            success: function(response) {
                                $('#department').append(response);
                            }
                        });

                        $('#department').on('change', function() {
                            var departmentID = $(this).val();
                            if (departmentID) {
                                $.ajax({
                                    type: 'POST',
                                    url: 'fetch_courses.php', // Assuming your PHP file is named fetch_courses.php
                                    data: { department_id: departmentID },
                                    success: function(html) {
                                        $('#course').html(html);
                                    }
                                });
                            } else {
                                $('#course').html('<option value="">Select Course</option>');
                            }
                        });
                    });
                </script>

    </body>
</html>