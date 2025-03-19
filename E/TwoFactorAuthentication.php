<?php
use RobThree\Auth\TwoFactorAuth;


class TwoFactorAuthentication {

    private Database $db;
    private $tfa;
    private $user;
    private $user_obj;
    private $login;

    public function __construct(){
        $this->tfa = new TwoFactorAuth;
        $this->db = Database::getInstance();
        $this->user = Session::getSession("uid");
        $this->user_obj = new User;
        $this->login = new Login;
    }

    private function generateSecret(){
        return $this->tfa->createSecret();
    }


    public function verifyCodeFromSecret( $secret, $code){
        return $this->tfa->verifyCode($secret, $code);
    }


    private function generateQRCodeFromSecret($secret){
        return $this->tfa->getQRCodeImageAsDataUri(APP_NAME , $secret); 
    }
    

    public function sendTfaResponse(){

        $secret = $this->generateSecret();
        $qrcode = $this->generateQRCodeFromSecret($secret);

        echo json_encode([
            "secret" => $secret,
            "qr_code" => $qrcode
        ]);
    }

    public function verifyUser($password){
        $sql = "SELECT password FROM users WHERE user_id = :i";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':i', $this->user, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $row = $stmt->fetch();
            $userPass = $row['password'];
            if (password_verify($password, $userPass)) {
                echo json_encode(["success" => true, "message" => "The password is verified sucessfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "The password is incorrect"]);
            }
        }
    }

    public function enableTfa($secret_key, $code)
    {
        if ($this->verifyCodeFromSecret($secret_key, $code)) {
            $sql = "UPDATE users SET tfa_secret = :key, tfa_enabled = 1 WHERE user_id = :u LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':key', $secret_key, PDO::PARAM_STR);
            $stmt->bindParam(':u', $this->user, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Two-factor authentication enabled successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to enable two-factor authentication"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "The code you provided is incorrect."]);
        }
    }

    public function tfaLogin($code){

        $id = Session::getSession('uid');

        $res = $this->user_obj->getUserData($id);
        $user_id = $res['user_id'];
        $secret = $res['tfa_secret'];

        if($this->verifyCodeFromSecret($secret, $code)){
            $this->login->updateUserLoginSession($user_id);
            $this->login->rememberDevice($user_id);
            Session::setSession("uid", $user_id);
            Session::setSession("isLoggedIn", true);
            Session::setSession('tfaChallenge', false);
            Session::setSession("login_fingerprint", Others::generateLoginString());
            echo json_encode(["success" => true, "message" => "Login successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect code."]);
        }
        
        

    }

    public function disableTfa()
    {

        try {

            $sql = "UPDATE users SET tfa_secret = NULL, tfa_enabled = 0 WHERE user_id = :u LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':u', $this->user, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Turned off two factor authentication successfully."]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to disable two-factor authentication."]);
            }

        } catch (Exception $e) {

            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);

        }

    }
    
    
}