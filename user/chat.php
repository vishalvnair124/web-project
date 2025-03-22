<?php
// Include database connection
include '../common/connection.php';

// Start the session to access session variables
session_start();
$logged_in_user_id = $_SESSION['user_id']; // Get the logged-in user's ID from the session

// Get the other user's ID from the URL
if (!isset($_GET['user_id'])) {
    die("User not specified."); // Stop execution if no user ID is provided
}
$chat_user_id = intval($_GET['user_id']); // Sanitize the user ID from the URL

// Fetch chat user details
$user_query = "SELECT name FROM users WHERE user_id = $chat_user_id"; // Query to get the name of the chat user
$user_result = $conn->query($user_query);
if ($user_result->num_rows == 0) {
    die("User not found."); // Stop execution if the user does not exist
}
$user = $user_result->fetch_assoc(); // Fetch the user details

// Fetch messages between the logged-in user and the chat user
$messages_query = "SELECT * FROM messages 
                   WHERE (sender_id = $logged_in_user_id AND receiver_id = $chat_user_id) 
                      OR (sender_id = $chat_user_id AND receiver_id = $logged_in_user_id)
                   ORDER BY sent_at ASC"; // Order messages by the time they were sent
$messages_result = $conn->query($messages_query); // Execute the query

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = $conn->real_escape_string($_POST['message']); // Sanitize the message input
    $insert_query = "INSERT INTO messages (sender_id, receiver_id, message, sent_at) 
                     VALUES ($logged_in_user_id, $chat_user_id, '$message', NOW())"; // Insert the new message into the database
    $conn->query($insert_query);

    // Redirect to refresh the chat and avoid resubmitting the form
    header("Location: chat.php?user_id=$chat_user_id");
    exit();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?= htmlspecialchars($user['name']) ?></title> <!-- Display the chat user's name in the title -->
    <link rel="stylesheet" href="styles.css"> <!-- External CSS -->

    <style>
        /* Basic styling for the chat interface */
        body {
            font-family: 'Arial', sans-serif;
            background: #ffc8c8;
            margin: 0;
            padding: 0;
        }

        .chat-container {
            max-width: 500px;
            margin: auto;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            border: 2px solid #d32f2f;
            margin-top: 50px;
            overflow: hidden;
        }

        .chat-header {
            background: var(--primary-color);
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 15px;
            background: #fff5f5;
        }

        .message {
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word;
        }

        .sent {
            background: #d32f2f;
            color: white;
            align-self: flex-end;
            margin-left: auto;
        }

        .received {
            background: white;
            border: 1px solid #d32f2f;
            color: #333;
            align-self: flex-start;
        }

        .chat-footer {
            display: flex;
            padding: 10px;
            background: white;
            border-top: 1px solid #ddd;
        }

        .message-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }

        .send-button {
            background: var(--primary-color);
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
        }

        .send-button:hover {
            background: #c62828;
        }
    </style>
</head>

<body>

    <div class="chat-container">
        <!-- Chat header displaying the chat user's name -->
        <div class="chat-header"><?= htmlspecialchars($user['name']) ?></div>

        <!-- Chat messages section -->
        <div class="chat-messages" id="chat-messages">
            <?php while ($msg = $messages_result->fetch_assoc()): ?>
                <div class="message <?= $msg['sender_id'] == $logged_in_user_id ? 'sent' : 'received' ?>">
                    <?= htmlspecialchars($msg['message']) ?> <!-- Display the message content -->
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Chat footer with a form to send new messages -->
        <div class="chat-footer">
            <form action="chat.php?user_id=<?= $chat_user_id ?>" method="post" style="display: flex; width: 100%;">
                <input type="text" name="message" class="message-input" placeholder="Type a message..." required>
                <button type="submit" class="send-button">Send</button>
            </form>
        </div>
    </div>

    <script>
        // Auto-scroll to the latest message
        let chatBox = document.getElementById('chat-messages');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>

</body>

</html>