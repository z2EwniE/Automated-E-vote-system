        
        <?php
                include_once __DIR__ . "/../../config/init.php";

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $partylistId = trim($_POST['partylist_id']);
                
                    if ($partylistId) {
                        try {
                            $stmt = $db->prepare("DELETE FROM partylists WHERE partylist_id = :partylist_id");
                            $stmt->bindParam(':partylist_id', $partylistId, PDO::PARAM_INT);
                            $stmt->execute();
                            echo "success";
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                    } else {
                        echo "Partylist ID is required.";
                    }
                }
                ?>
