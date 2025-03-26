        <?php

                $servername = "139.99.97.250";  
                $username = "evote";        
                $password = "2Ty4th4TVHnTUFsL";             
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
