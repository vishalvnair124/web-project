<?php
// Include the database connection file
include '../common/connection.php';

// Start the session to access session variables
session_start();

// Get the sender's user ID from the session
$sender_id = $_SESSION['user_id'];

// Get the receiver's user ID from the POST request and sanitize it
$receiver_id = intval($_POST['receiver_id']);

// Get the message content from the POST request and trim any extra whitespace
$message = trim($_POST['message']);

// Check if the message is not empty
if ($message !== "") {
    // Escape special characters in the message to prevent SQL injection
    $safe_message = $conn->real_escape_string($message);

    // Insert the message into the database with the sender, receiver, and timestamp
    $query = "INSERT INTO messages (sender_id, receiver_id, message, sent_at) 
              VALUES ($sender_id, $receiver_id, '$safe_message', NOW())";

    // Execute the query to save the message
    $conn->query($query);
}
