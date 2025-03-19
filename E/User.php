<?php

class User
{

    /**
     * @var Database
     */
    private Database $db;
   // private  $user;
    private Login $login;

    private $user;


    public function __construct(){
        $this->db = Database::getInstance();
        $this->user = Session::getSession("uid");
        $this->login = new Login();
        
    }

    /**
     * Get logged in user data
     * @return mixed
     */
    public function getUserDetails(){

        $sql = "SELECT * FROM `users` INNER JOIN `user_details` ON user_details.user_id = users.user_id WHERE users.user_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam("uid", $this->user, PDO::PARAM_INT);
        $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Get user data
     * @return mixed
     */
    public function getUserData($id){

        $sql = "SELECT * FROM `users` INNER JOIN `user_details` ON user_details.user_id = users.user_id WHERE users.user_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam("uid", $id, PDO::PARAM_INT);
        $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Get user data by email
     * @return mixed
     */
    public function getUserDataByEmail($email){

        $sql = "SELECT * FROM `users` INNER JOIN `user_details` ON user_details.user_id = users.user_id WHERE users.email = :em";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":em", $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return 0;
        }



    }

    /***
     * Change profile function
     * @return boolean TRUE if profile edit is good, FALSE OTHERWISE
     */
    public function editProfile($avatar, $first_name,  $last_name,  $birthdate)
    {

        if (!$this->login->isLoggedIn()){
            return false;
        }

        $user = $this->getUserData($this->user);

        if (!empty($avatar)) {

            if ( 0 < $avatar['error'] ) {
                echo 'Error: ' . $avatar['error'];
                return false;
            }

            $info = getimagesize($avatar['tmp_name']);
            if ($info === FALSE) {
                echo json_encode(["success" => false, "message" => "Unable to determine image type of uploaded file."]);

                return false;
            }

            if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
                echo json_encode(["success" => false, "message" => "Not a gif/jpeg/png."]);

                return false;
            }

            $avatar = 'uploads/avatars/' . Others::uploadAvatar($avatar);
        } else {
            $avatar = $user['avatar'];
        }




        $first_name = trim($first_name);
        $last_name = trim($last_name);
        $birthdate = trim($birthdate);

        $sql = "UPDATE `user_details` SET `avatar` = :avatar, `first_name` = :first_name, `last_name` = :last_name, `birthdate` = :birthdate WHERE user_id = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":avatar", $avatar, PDO::PARAM_STR);
        $stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
        $stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
        $stmt->bindParam(":birthdate", $birthdate, PDO::PARAM_STR);
        $stmt->bindParam(":uid", $this->user, PDO::PARAM_STR);

        if($stmt->execute()){
            echo json_encode(["success" => true, "message" => "Saved successfully."]);

            return true;
        }
            echo json_encode(["success" => false, "message" => "An error has occured."]);

            return false;
    }

    /**
     * Make changes to user email
     * @param string $email User email
     * @return bool
     */
    public function changeEmail(string $email)
    {

            if (!$this->login->isLoggedIn()){
                return false;

            }

            $email = trim($email);

            $userDetails = $this->getUserDetails();

            if ($email == $userDetails['email']){
                echo json_encode(["success" => false, "message" => "You can't use your current email."]);
                return false;
            }

            if($this->login->checkEmail($email)){
                echo json_encode(["success" => false, "message" => "Email is already exists."]);
                return false;
            }

            $sql = "UPDATE `users` SET `email` = :email WHERE `user_id` = :uid";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":uid", $this->user, PDO::PARAM_STR);
            if($stmt->execute()){

                echo json_encode(["success" => true, "message" => "Saved successfully."]);

                return true;

            }


            echo json_encode(["success" => false, "message" => "An error has occured."]);

            return false;

        }



    /***
     * Change password function
     * @param $oldPassword string User's password
     * @param $newPassword string User's new password
     * @param $confirmPassword string User's confirmation new password
     * @return boolean TRUE if success, FALSE otherwise
     */
    public function changePassword(string $oldPassword, string $newPassword, string $confirmPassword){

        if (!$this->login->isLoggedIn()){
            return false;
        }

        try {

        $oldPassword = trim($oldPassword);
        $newPassword = trim($newPassword);
        $confirmPassword =  trim($confirmPassword);

        $u = $this->getUserDetails();
        $hashed_password = $u['password'];

            if(password_verify($oldPassword, $hashed_password)){

                $new_hashed_password = password_hash($confirmPassword, PASSWORD_ARGON2ID);

                $sql = "UPDATE `users` SET `password` = :password WHERE `user_id` = :uid";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(":password", $new_hashed_password, PDO::PARAM_STR);
                $stmt->bindParam(":uid", $this->user, PDO::PARAM_INT);

                if ($stmt->execute()){
                    echo json_encode(["success" => true, "message" => "Change password successfully."]);    
                    return true;
                }

            } else {
                echo json_encode(["success" => false, "message" => "Old password is incorrect."]);


            }
        } catch (Exception $e){
            echo json_encode(["success" => false, "message" => "Error: Something happened to our end."]);
        }

    }


    

}
