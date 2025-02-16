<?php
if (session_status() == PHP_SESSION_NONE) {
    // If the session is not started, start it
    session_start();
}
if (!isset($_SESSION['Admin_email'])) {
    header("Location: ../admin/adminlogin.php");
    exit();
}
