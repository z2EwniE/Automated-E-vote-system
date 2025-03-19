<?php
/**
 * Login Class
 */
class Login {
    /**
     * @var Database
     */
    private Database $db;
    public function __construct() {
        $this->db = Database::getInstance();
    }
    /***
     * Check the user if logged in
     * @return bool
    */
    public function isLoggedIn() {
        $loginString = Others::generateLoginString();
        $currentString = Session::checkSession("login_fingerprint");
        if (Session::checkSession("isLoggedIn") === true || $loginString === $currentString) {
            return true;
        } else {
            Session::unsetSession('isLoggedIn');
            Session::unsetSession('login_fingerprint');
            return false; // User is not logged in

        }
    }
    public function isTfaLoggedIn() {
        if (Session::checkSession('tfaChallenge')) {
            if (Session::checkSession('uid')) {
                return true;
            }
        }
        return false;
    }
    public function isRememberSet(){
        // Check if the "remember_me" cookie exists
        if (Cookie::get('remember_me')) {
            // Retrieve the user ID from the cookie
            $user_id = Cookie::get('remember_me');
            // Check if the user ID is not empty
            if (!empty($user_id)) {
                return true; // Remember me is set and contains a valid user ID
            }
        }
        return false; // Remember me is not set or contains an invalid user ID
    }
    
    /**
     * User login function
     * @param string $username User's username
     * @param string $password User's password
     * @param string $token login token
     * @param int $remember remmember me
     * @return boolean TRUE if okay, FALSE otherwise
     *
     */
    public function userLogin(string $username, string $password, string $token, bool $remember) {
        $username = trim($username);
        $password = trim($password);
        try {
            if (!CSRF::check($token, 'login_form')) {
                echo json_encode(["success" => false, "message" => "Unable to process your request."]);
                return false;
            }
            $sql = "SELECT * FROM `users` WHERE `username` = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    /** @var string $hashed_password **/
                    $hashed_password = $row['password'];
                    $user_id = $row['user_id'];
                    if ($this->isBruteForce($user_id)) {
                        echo json_encode(["success" => false, "message" => "You have exceeded the maximum login attempts."]);
                        return false;
                    }
                    if ($row['status'] == '0') {
                        echo json_encode(["success" => false, "message" => "Your account has not been activated yet. Please confirm your account."]);
                        return false;
                    }
                    if (password_verify($password, $hashed_password)) {

                        if(!empty($remember)){
                            Cookie::set('remember_me', true, '+30 days');
                            $uid = Others::encryptData($user_id, ENCRYPTION_KEY);
                            Cookie::set('uid', $uid, '+30 days');
                        }

                        // Check if the user has logged in before using this device
                        if ($this->checkSessionFingerprint($user_id)) {
                            // Log in the user without TFA if session fingerprint exists
                            Session::setSession("uid", $user_id);
                            Session::setSession("isLoggedIn", true);
                            Session::setSession('tfaChallenge', false);
                            Session::setSession("login_fingerprint", Others::generateLoginString());
                            $this->updateUserLoginSession($user_id);
                            echo json_encode(["success" => true, "message" => "Login successfully.", "tfa" => false]);
                            return true;
                        }
                        if(!$this->isDeviceRemembered($user_id)){
                            // TFA handling if session fingerprint doesn't exist
                            if ($row['tfa_enabled'] == 1) {
                                // Two-factor authentication enabled
                                Session::setSession('tfaChallenge', true);
                                Session::setSession("uid", $user_id);
                                echo json_encode(["success" => true, "message" => "Two-factor authentication enabled. Please verify your identity.", "tfa" => true]);
                                return true;
                            }
                       }
                        // Log in the user normally
                        Session::setSession("uid", $user_id);
                        Session::setSession("isLoggedIn", true);
                        Session::setSession('tfaChallenge', false);
                        Session::setSession("login_fingerprint", Others::generateLoginString());
                        $this->updateUserLoginSession($user_id);
                        echo json_encode(["success" => true, "message" => "Login successfully.", "tfa" => false]);
                        return true;
                    } else {
                        echo json_encode(["success" => false, "message" => "Password is incorrect."]);
                        $this->increaseLoginAttempt($user_id);
                        return false;
                    }
                } else {
                    echo json_encode(["success" => false, "message" => "No username found."]);
                    return false;
                }
            } else {
                echo json_encode(["success" => false, "message" => "Error occurred while logging in."]);
                return false;
            }
        }
        catch(Exception $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage() ]);
            return false;
        }
    }

    private function isDeviceRemembered($user_id) {
        // Check if the 'remembered_device' cookie exists and if its decrypted content matches the provided user ID
        return Cookie::check('remembered_device') && Others::decryptData(Cookie::get('remembered_device'), ENCRYPTION_KEY) == $user_id;
    }
    
    public function rememberDevice($user_id) {
        // Encrypt the user ID and set it in the 'remembered_device' cookie to remember the device
        $encrypted_user_id = Others::encryptData($user_id, ENCRYPTION_KEY);
        Cookie::set('remembered_device', $encrypted_user_id, '+30 days');
    }
    
    public function updateUserLoginSession($user_id) {
        $date = date('Y-m-d H:i:s');
        $currentFingerprint = Others::generateLoginString();
        $userSession = $this->checkSessionFingerprint($user_id);
    
        $user_agent = Others::getUserAgent();
        $ip_address = Others::getUserIpAddress();
    
        $sql = 'SELECT * FROM login_sessions WHERE user_id = :u';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':u', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();
    
        $userFingerprint = (!empty($res['fingerprint'])) ? $res['fingerprint'] : '';
    
        if ($userSession) {
            $stmt = $this->db->prepare('UPDATE login_sessions SET datetime = :d WHERE fingerprint = :fp AND user_id = :u');
            $stmt->bindParam(':fp', $userFingerprint, PDO::PARAM_STR);
            $stmt->bindParam(':u', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':d', $date, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $stmt = $this->db->prepare("INSERT INTO login_sessions (user_id, fingerprint, user_agent, ip_address, datetime) VALUES (:u, :fp, :ua, :ip, :d)");
            $stmt->bindParam(':fp', $currentFingerprint, PDO::PARAM_STR);
            $stmt->bindParam(':ip', $ip_address, PDO::PARAM_STR);
            $stmt->bindParam(':ua', $user_agent, PDO::PARAM_STR);
            $stmt->bindParam(':u', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':d', $date, PDO::PARAM_STR);
            $stmt->execute();
        }
    }
    
    /**
     * Check user session in database
     * @param int $user_id ID of user
     * @return boolean TRUE if session exists, FALSE otherwise
     */
    private function checkSessionFingerprint($user_id) {
        $currentFingerprint = Others::generateLoginString();
        $sql = 'SELECT COUNT(*) FROM login_sessions WHERE user_id = :u AND fingerprint = :fp';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':u', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':fp', $currentFingerprint, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Check username if exists
     * @param $username string username of the user
     * @return boolean TRUE if it exists, FALSE otherwise
     */
    public function checkUsername($username) {
        $sql = "SELECT user_id FROM users WHERE username = :u";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':u', $username, PDO::PARAM_STR);
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
    /**
     * Check email if exists
     * @param $email string email of the user
     * @return boolean TRUE if it exists, FALSE otherwise
     */
    public function checkEmail(string $email) {
        $sql = "SELECT `user_id` FROM `users` WHERE `email` = :e";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':e', $email, PDO::PARAM_STR);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                return true;
            }
        }
    }
    /**
     * Increase log in attempt when password is incorrect
     * @return void
     */
    private function increaseLoginAttempt($user_id) {
        $date = date('Y-m-d');
        $user_ip = Others::getUserIpAddress();
        $login_attempts = $this->getLoginAttempts($user_id);
        // echo $login_attempts;
        if ($login_attempts > 0) {
            $stmt = $this->db->prepare('UPDATE `login_attempts` SET `attempt` = attempt + 1 WHERE `user_id` = :uid AND `ip_address` = :ip AND `date` = :d');
            // $stmt->execute([$user_ip,$date]);
            $stmt->bindParam(':ip', $user_ip, PDO::PARAM_STR);
            $stmt->bindParam(':d', $date, PDO::PARAM_STR);
            $stmt->bindParam(':uid', $user_id);
            $stmt->execute();
        } else {
            $stmt = $this->db->prepare("INSERT INTO `login_attempts` (`user_id`, `ip_address`, `date`) VALUES (:uid, :ip, :d)");
            $stmt->bindParam(':ip', $user_ip, PDO::PARAM_STR);
            $stmt->bindParam(':d', $date, PDO::PARAM_STR);
            $stmt->bindParam(':uid', $user_id);
            $stmt->execute();
        }
    }
    /**
     * Get user log in attempt
     * @return int
     */
    private function getLoginAttempts($user_id) {
        $date = date('Y-m-d');
        $user_ip = Others::getUserIpAddress();
        $sql = "SELECT * FROM `login_attempts` WHERE `user_id` = :uid AND `ip_address` = :ip AND `date` = :d";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':ip', $user_ip, PDO::PARAM_STR);
        $stmt->bindParam(':d', $date, PDO::PARAM_STR);
        $stmt->bindParam(':uid', $user_id);
        $stmt->execute();
        $res = $stmt->fetch();
        if ($stmt->rowCount() == 0) {
            return 0;
        } else {
            return intval($res['attempt']);
        }
    }
    /**
     * Check if exceeds the number of max attempts
     * @return bool
     */
    private function isBruteForce($user_id) {
        $login_attempts = $this->getLoginAttempts($user_id);
        if ($login_attempts > MAX_LOGIN_ATTEMPTS) {
            return true;
        } else {
            return false;
        }
    }
}
