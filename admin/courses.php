        <?php
            include_once __DIR__ . "/../config/init.php";

        try {
            $dept_id = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

            $sql = "SELECT * FROM course WHERE course_department = :dept_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':dept_id', $dept_id, PDO::PARAM_INT);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<button class='course-button' data-course-id='{$row['course_id']}'>{$row['course_name']}</button>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
