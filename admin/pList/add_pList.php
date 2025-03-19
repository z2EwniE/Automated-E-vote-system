            
            <?php
           include_once __DIR__ . "/../../config/init.php";

           if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $partylistName = trim($_POST['partylist_name']);
            $department = trim($_POST['department']);
        
            if ($partylistName && $department) {
                try {
                    $stmt = $db->prepare("INSERT INTO partylists (partylist_name, department) VALUES (:partylist_name, :department)");
                    $stmt->bindParam(':partylist_name', $partylistName, PDO::PARAM_STR);
                    $stmt->bindParam(':department', $department, PDO::PARAM_INT);
                    $stmt->execute();
                    echo "success";
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                echo "Partylist name and department cannot be empty.";
            }
        }
        ?>
