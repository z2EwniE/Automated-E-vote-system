<?php

include_once __DIR__ . "/../config/init.php";

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$years = ['1st', '2nd', '3rd', '4th'];
$students_per_page = 10;

foreach ($years as $year) {
    $year_level = str_replace(['st', 'nd', 'rd', 'th'], '', $year);
    $page = isset($_GET["page_$year_level"]) ? intval($_GET["page_$year_level"]) : 1;
    $offset = ($page - 1) * $students_per_page;

    echo "<div class='year-card'>
            <div class='year-header'>
                <h2 class='year-title'>{$year} Year</h2>
                <div class='search-container'>
                    <input type='text' class='search-input' placeholder='Search students...'>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Student ID Number</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>";

    $sql = "SELECT student_id, CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name, ' ', COALESCE(suffix_name, '')) AS full_name 
            FROM students 
            WHERE course = ? AND year_level = ?
            ORDER BY last_name, first_name 
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $course_id, $year_level, $students_per_page, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $count = $offset + 1;
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$count}</td>
                    <td>{$row['student_id']}</td>
                    <td>{$row['full_name']}</td>
                </tr>";
            $count++;
        }
    } else {
        echo "<tr class='empty-message'><td colspan='3'>No students found</td></tr>";
    }

    echo "</tbody></table>";

    $total_students_query = "SELECT COUNT(*) as total FROM students WHERE course = ? AND year_level = ?";
    $stmt_total = $conn->prepare($total_students_query);
    $stmt_total->bind_param("is", $course_id, $year_level);
    $stmt_total->execute();
    $total_students_result = $stmt_total->get_result();
    $total_students = $total_students_result->fetch_assoc()['total'];
    $total_pages = ceil($total_students / $students_per_page);

    echo "<div class='pagination'>";
    if ($page > 1) {
        $prev_page = $page - 1;
        echo "<a href='?course_id=$course_id&page_$year_level=$prev_page' class='btn-prev'>Previous</a>";
    }
    if ($page < $total_pages) {
        $next_page = $page + 1;
        echo "<a href='?course_id=$course_id&page_$year_level=$next_page' class='btn-next'>Next</a>";
    }
    echo "</div></div>";

    $stmt->close();
    $stmt_total->close();
}
$conn->close();
?>
