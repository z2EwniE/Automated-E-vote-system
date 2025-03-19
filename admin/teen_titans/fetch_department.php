        <?php
        include_once __DIR__ . '/../../config/init.php';

        try {
            // Fetch departments
            $sql = "SELECT * FROM department";
            $stmt = $db->query($sql);

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . htmlspecialchars($row['department_id']) . '">' . htmlspecialchars($row['department_name']) . '</option>';
                }
            } else {
                echo '<option value="">No departments available</option>';
            }
        } catch (PDOException $e) {
            echo '<option value="">Error: ' . htmlspecialchars($e->getMessage()) . '</option>';
        }
        ?>
