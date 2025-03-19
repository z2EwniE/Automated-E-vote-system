        <?php
        include_once __DIR__ . '/../../config/init.php';

        header('Content-Type: application/json');

        try {
            // Fetch year levels
            $sql = "SELECT DISTINCT year_level FROM students"; // Update the table and column names to match your schema
            $stmt = $db->query($sql);

            $year_levels = [];
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $year_levels[] = $row['year_level'];
                }
            }

            echo json_encode(['year_levels' => $year_levels]);
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        ?>
