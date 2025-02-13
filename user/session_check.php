<?php
if (session_status() == PHP_SESSION_NONE) {
    // If the session is not started, start it
    session_start();
}
if (!(isset($_SESSION["user_email"]) && $_SESSION["isLogined"] === true)) {
    header("Location: ../login/");
    exit();
}
