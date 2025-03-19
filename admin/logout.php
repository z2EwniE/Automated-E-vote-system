<?php
include_once __DIR__ . "/../config/init.php";

Session::unsetSession("uid");
Session::unsetSession("isLoggedIn");

header("Location: index.php");