<?php

include_once __DIR__ . '/../../config/init.php';

header('Content-Type: application/json');



try {

   
    // Get the year range from the query string (e.g., "2023-2024")
    $yearRange = isset($_GET['yearRange']) ? $_GET['yearRange'] : '';
    list($startYear, $endYear) = explode('-', $yearRange);

    $sql = "SELECT 
                DATE_FORMAT(voted_at, '%b') AS month,
                MONTH(voted_at) AS month_number,
                COUNT(vote_id) AS total_votes
            FROM votes
            WHERE YEAR(voted_at) BETWEEN :startYear AND :endYear
            GROUP BY YEAR(voted_at), MONTH(voted_at)
            ORDER BY MONTH(voted_at)";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':startYear', $startYear, PDO::PARAM_INT);
    $stmt->bindParam(':endYear', $endYear, PDO::PARAM_INT);
    $stmt->execute();

    $labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    $data = array_fill(0, 12, 0);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[$row['month_number'] - 1] = (int) $row['total_votes'];
    }

    echo json_encode([
        "labels" => $labels,
        "data" => $data
    ]);

} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
