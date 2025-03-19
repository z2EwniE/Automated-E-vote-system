<?php
include_once __DIR__ . '/../../config/init.php';

header('Content-Type: application/json');

try {
    // Fetch votes data based on dynamic filters (optional)
    $department = isset($_GET['department']) ? $_GET['department'] : '';
    $course = isset($_GET['course']) ? $_GET['course'] : '';
    $year_level = isset($_GET['year_level']) ? $_GET['year_level'] : '';

    // Base query with join to the students table
    $query = "
        SELECT 
            CONCAT(students.first_name, ' ', students.middle_name, ' ', students.last_name) AS candidate_name, 
            positions.position_name,
            COUNT(votes.vote_id) AS total_votes
        FROM votes
        INNER JOIN students ON votes.student_id = students.id
        INNER JOIN candidates ON votes.candidate_id = candidates.candidate_id
        INNER JOIN positions ON candidates.candidate_position = positions.position_id
        WHERE 1
    ";

    // Add conditions dynamically
    $params = [];

    if (!empty($department)) {
        $query .= " AND students.department = :department";
        $params[':department'] = $department;
    }
    if (!empty($course)) {
        $query .= " AND students.course = :course";
        $params[':course'] = $course;
    }
    if (!empty($year_level)) {
        $query .= " AND students.year_level = :year_level";
        $params[':year_level'] = $year_level;
    }

    // Group by candidate_id, position_name, and candidate_name
    $query .= " GROUP BY candidates.candidate_id, positions.position_name, candidates.student_id;";

    $stmt = $db->prepare($query);

    // Bind parameters dynamically
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }

    $stmt->execute();
    $votesData = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $votesData[] = [
            'candidate_name' => $row['candidate_name'],
            'position_name' => $row['position_name'],
            'total_votes' => $row['total_votes']
        ];
    }

    echo json_encode(['votes' => $votesData]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>