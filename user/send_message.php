<?php
include '../common/connection.php';
session_start();

$sender_id = $_SESSION['user_id'];
$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);

if ($message !== "") {
    $safe_message = $conn->real_escape_string($message);
    $query = "INSERT INTO messages (sender_id, receiver_id, message, sent_at) 
              VALUES ($sender_id, $receiver_id, '$safe_message', NOW())";
    $conn->query($query);
}
