<?php
        include_once __DIR__ . '/../../config/init.php';

        if(isset($_POST['action'])) {

            $student_id = $_POST['student_id'];

            try {

                $sql = "SELECT 
    positions.position_name,
    partylists.partylist_name,
    CONCAT(students.first_name, ' ', students.middle_name, ' ', students.last_name) AS candidate_name,
    votes.voted_at
FROM 
    votes
INNER JOIN 
    candidates ON candidates.candidate_id = votes.candidate_id
INNER JOIN 
    students ON students.id = votes.candidate_id  -- Assuming `candidates` has a `student_id` field that links to `students`
INNER JOIN 
    course ON students.course = course.course_id
INNER JOIN 
    department ON department.department_id = students.department
INNER JOIN 
    positions ON positions.position_id = votes.position_id
INNER JOIN 
    partylists ON partylists.partylist_id = votes.partylist_id
WHERE 
    votes.student_id = :id 
ORDER BY 
    votes.voted_at;";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(":id", $student_id);
                if($stmt->execute()){

                    echo json_encode(
                        [
                            "success" => true,
                            "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
                        ]
                    );

                }

            } catch(Exception $e){

            }


        }