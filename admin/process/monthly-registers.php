<?php
        include_once __DIR__ . '/../../config/init.php';

header('Content-Type: application/json');


try {

    // Modified query to get current year's data
    $sql = "SELECT 
                DATE_FORMAT(date_created, '%b') AS month,
                MONTH(date_created) AS month_number,
                COUNT(id) AS total_students_registered
            FROM students 
            WHERE YEAR(date_created) = YEAR(CURRENT_DATE())
            GROUP BY MONTH(date_created)
            ORDER BY MONTH(date_created)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    $data = array_fill(0, 12, 0); // Initialize all months with 0

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[$row['month_number'] - 1] = (int) $row['total_students_registered'];
    }

    echo json_encode([
        "labels" => $labels,
        "data" => $data
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "error" => "Database connection failed: " . $e->getMessage()
    ]);
}
