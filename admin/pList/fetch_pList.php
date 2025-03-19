<?php
    include_once __DIR__ . "/../../config/init.php";

    if($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id = $_POST['partylist_id'];

        $stmt = $db->prepare("SELECT * FROM partylists WHERE partylist_id = :pid");
        $stmt->bindParam(":pid", $id);
        if($stmt->execute()){

            echo json_encode($row = $stmt->fetch(PDO::FETCH_ASSOC));

        }

    }

    