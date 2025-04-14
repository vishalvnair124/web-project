<?php
include '../common/connection.php';
session_start();

$logged_in_user_id = $_SESSION['user_id'];
$chat_user_id = intval($_GET['user_id']);

$messages_query = "SELECT * FROM messages 
                   WHERE (sender_id = $logged_in_user_id AND receiver_id = $chat_user_id) 
                      OR (sender_id = $chat_user_id AND receiver_id = $logged_in_user_id)
                   ORDER BY sent_at ASC";
$messages_result = $conn->query($messages_query);

while ($msg = $messages_result->fetch_assoc()) {
    $class = $msg['sender_id'] == $logged_in_user_id ? 'sent' : 'received';
    echo '<div class="message ' . $class . '">' . htmlspecialchars($msg['message']) . '</div>';
}
