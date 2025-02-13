<?php
// Include the database connection file
include('../common/connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT Admin_password FROM admin WHERE Admin_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    // Verify the password
    if ($hashed_password && password_verify($password, $hashed_password)) {
        // Password is correct, start a session and redirect to admin dashboard
        session_start();
        $_SESSION['Admin_email'] = $email;
        header("Location: index.php");
        exit();
    } else {
        // Invalid email or password
        header("Location: adminlogin.php?error=Invalid email or password");
        exit();
    }
} else {
    // Redirect to login page if the form is not submitted
    header("Location: adminlogin.php");
    exit();
}
