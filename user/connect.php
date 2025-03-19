        <?php

$servername = "localhost";  
$username = "root";        
$password = "";             
$dbname = "evote";    
        //$servername = "139.99.97.250";  
        //$username = "evote";        
       // $password = "TacHIuuWOuhPS!Oh";             
        //$dbname = "evote";         


        $conn = new mysqli($servername, $username, $password, $dbname);


        if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
        }
        
        ?>
