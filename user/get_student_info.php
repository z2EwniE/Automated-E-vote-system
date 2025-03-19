<?php

header('Content-Type: application/json');

$csvFile = '../students.csv'; 
function getStudentInfo($student_id, $csvFile) {
    if (!file_exists($csvFile) || !is_readable($csvFile)) {
        return ['success' => false, 'message' => 'CSV file not found or not readable.'];
    }

    $header = null;
    $data = [];

    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (!$header) {
                $header = $row;
                continue;
            }
            $row = array_combine($header, $row);
            if (strcasecmp($row['studentID'], $student_id) === 0) {
                fclose($handle);
                return ['success' => true, 'data' => $row];
            }
        }
        fclose($handle);
    }

    return ['success' => false, 'message' => 'Student ID not found.'];
}

$student_id = '';
if (isset($_GET['student_id'])) {
    $student_id = trim($_GET['student_id']);
} elseif (isset($_POST['student_id'])) {
    $student_id = trim($_POST['student_id']);
}

if (!preg_match("/^[A-Za-z0-9\-]+$/", $student_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Student ID format.']);
    exit();
}

if ($student_id === '') {
    echo json_encode(['success' => false, 'message' => 'No Student ID provided.']);
    exit();
}

$result = getStudentInfo($student_id, $csvFile);

echo json_encode($result);
?>
