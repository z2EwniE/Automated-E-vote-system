        <?php
        include 'connect.php';  // Include the database connection file

        // SQL query to count the total number of IDs from the students table
        $sql = "SELECT COUNT(id) AS total_count FROM students";
        $result = $conn->query($sql);

        // Initialize variable to store the total count
        $total_count = 0;

        // Check if the query returned any result
        if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_count = $row['total_count'];
        }

        // Close the database connection
        $conn->close();
        ?>
