        <?php

        include 'connect.php';


        if ($conn->connect_error) {
            die("conn failed: " . $conn->connect_error);
        }
        
        if (isset($_POST['department_id'])) {
            $department_id = $_POST['department_id'];
            
            // Fetch courses based on the selected department
            $sql = "SELECT * FROM course WHERE course_department = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $department_id);
            $stmt->execute();
            $result = $stmt->get_result();

            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<option value="'.$row['course_id'].'">'.$row['course_name'].'</option>';
                }
            } else {
                echo '<option value="">No courses available</option>';
            }

            $stmt->close();
        }

        $conn->close();
        ?>
