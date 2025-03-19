        <?php
        include_once __DIR__ . '/../../config/init.php';

        try {
            if (isset($_POST['department_id'])) {
                $department_id = $_POST['department_id'];

                // Fetch courses based on the selected department
                $sql = "SELECT * FROM course WHERE course_department = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$department_id]);

                if ($stmt->rowCount() > 0) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($row['course_id']) . '">' . htmlspecialchars($row['course_name']) . '</option>';
                    }
                } else {
                    echo '<option value="">No courses available</option>';
                }
            }
        } catch (PDOException $e) {
            echo '<option value="">Error: ' . htmlspecialchars($e->getMessage()) . '</option>';
        }
        ?>
