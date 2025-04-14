<?php
// Include the database connection file
include '../common/connection.php';

// Start the session to access session variables
session_start();

// Get the logged-in user's ID from the session
$logged_in_user_id = $_SESSION['user_id'];

// Get the chat user's ID from the URL parameter and sanitize it
$chat_user_id = intval($_GET['user_id']);

// Query to fetch messages between the logged-in user and the chat user
$messages_query = "SELECT * FROM messages 
                   WHERE (sender_id = $logged_in_user_id AND receiver_id = $chat_user_id) 
                      OR (sender_id = $chat_user_id AND receiver_id = $logged_in_user_id)
                   ORDER BY sent_at ASC"; // Order messages by the time they were sent (ascending)
$messages_result = $conn->query($messages_query);

// Loop through each message and display it
while ($msg = $messages_result->fetch_assoc()) {
    // Determine the message class based on the sender (sent or received)
    $class = $msg['sender_id'] == $logged_in_user_id ? 'sent' : 'received';

    // Display the message with proper escaping to prevent XSS
    echo '<div class="message ' . $class . '">' . htmlspecialchars($msg['message']) . '</div>';
}
