<?php
session_start();

// Unset all of the session variables
$_SESSION = array();

// If session cookies are used, delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: ../login/");
exit(); // Ensure no further code is executed after redirect
