<?php

    include '../Evote/config/init.php';

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        // Fetch student vote logs with their completed votes
        $sql = "SELECT  students.student_id, 
                        students.first_name, 
                        students.middle_name,
                        students.last_name, 
                        students.course, 
                        students.year_level, 
                        students.department,
                    COUNT(votes.id) AS completed_votes
            FROM students
            LEFT JOIN votes ON students.id_number = votes.student_id AND votes.status = 'completed'
            GROUP BY students.id_number, students.name, students.course_year";
        
        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>