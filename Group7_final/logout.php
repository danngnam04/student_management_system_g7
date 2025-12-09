<?php
// logout.php

session_start();
// unset session
$_SESSION = array();
// destroy the session
session_destroy();


//cookie
setcookie('remember_login', '', time() - 3600, "/");

// redirect to login page
header('Location: login.php');
exit;
?>
