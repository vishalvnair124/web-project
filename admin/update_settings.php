<?php
include '../common/connection.php';
session_start();

// In production, get from session
$admin_id = 1;

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];

if (empty($name) || empty($email)) {
    echo "Name and Email are required!";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email format.";
    exit;
}

if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE admin SET Admin_name = ?, Admin_email = ?, Admin_password = ? WHERE Admin_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $name, $email, $hashed, $admin_id);
} else {
    $query = "UPDATE admin SET Admin_name = ?, Admin_email = ? WHERE Admin_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $name, $email, $admin_id);
}

if ($stmt->execute()) {
    echo "Settings updated successfully.";
} else {
    echo "Failed to update settings. Please try again.";
}
