        <?php
        //const DB_HOST = "localhost";
        //const DB_USER = "root";
        //const DB_PASS = "";
        //const DB_NAME = "evote";

        const DB_HOST = "139.99.97.250";
        const DB_USER = "evote";
        const DB_PASS = "2Ty4th4TVHnTUFsL";
        const DB_NAME = "evote";

        const APP_NAME = "E-Vote System";

        const APP_URL = "http://localhost/E-vote/";
        const EMAIL_CONFIRMATION = false;
        const MAX_LOGIN_ATTEMPTS = 12;

        const IS_SMTP = true;
        const SMTP_HOST = "smtp.gmail.com";
        const SMTP_USERNAME = "ssc.evotesystem@gmail.com";
        const SMTP_PASSWORD = "atkl zkkc djhk tpml";
        const SMTP_ENCRYPTION = "tls";

        const AUTO_LOGOUT_TIME = 1800; // 30 minutes
        const LOGOUT_REDIRECT = 'login.php';

        const ENCRYPTION_KEY = "YykJDLXLzeCscp7jPU/Fo65393YbmvgL0yEj4BSXkrXoaMFOBZfDjxv/eVDUMYcg"; // Remember me cookie encryption key

        date_default_timezone_set('Asia/Manila');
        error_reporting(E_ALL);