    <?php
    include 'connect.php';



    if ($conn->connect_error) {
        die("conn failed: " . $conn->connect_error);
    }

    // Fetch departments
    $sql = "SELECT * FROM department";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<option value="'.$row['department_id'].'">'.$row['department_name'].'</option>';
        }
    } else {
        echo '<option value="">No departments available</option>';
    }

    $conn->close();
    ?>
