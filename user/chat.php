<?php
// Include the database connection file
include '../common/connection.php';

// Start the session to access session variables
session_start();

// Get the logged-in user's ID from the session
$logged_in_user_id = $_SESSION['user_id'];

// Check if the user_id parameter is provided in the URL
if (!isset($_GET['user_id'])) {
    die("User not specified."); // Terminate if no user_id is provided
}

// Sanitize and store the chat user's ID
$chat_user_id = intval($_GET['user_id']);

// Query to fetch the name of the user to chat with
$user_query = "SELECT name FROM users WHERE user_id = $chat_user_id";
$user_result = $conn->query($user_query);

// Check if the user exists in the database
if ($user_result->num_rows == 0) {
    die("User not found."); // Terminate if the user does not exist
}

// Fetch the user's details
$user = $user_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chat with <?= htmlspecialchars($user['name']) ?></title>
    <!-- Link to external stylesheet -->
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        /* Basic styling for the page */
        body {
            font-family: 'Arial', sans-serif;
            background: #ffc8c8;
            margin: 0;
            padding: 0;
        }

        /* Styling for the chat container */
        .chat-container {
            max-width: 500px;
            margin: auto;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            border: 2px solid #d32f2f;
            margin-top: 50px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 80vh;
        }

        /* Header styling for the chat */
        .chat-header {
            background: #d32f2f;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            position: relative;
        }

        /* Back button styling */
        .back-button {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            color: #d32f2f;
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        /* Styling for the chat messages area */
        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            background: #fff5f5;
            display: flex;
            flex-direction: column;
        }

        /* Styling for individual messages */
        .message {
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word;
        }

        /* Sent message styling */
        .sent {
            background: #d32f2f;
            color: white;
            align-self: flex-end;
            margin-left: auto;
        }

        /* Received message styling */
        .received {
            background: white;
            border: 1px solid #d32f2f;
            color: #333;
            align-self: flex-start;
        }

        /* Footer styling for the chat */
        .chat-footer {
            display: flex;
            padding: 10px;
            background: white;
            border-top: 1px solid #ddd;
        }

        /* Input field styling for messages */
        .message-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }

        /* Send button styling */
        .send-button {
            background: #d32f2f;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
        }

        /* Hover effect for the send button */
        .send-button:hover {
            background: #c62828;
        }
    </style>
</head>

<body>

    <!-- Chat container -->
    <div class="chat-container">
        <!-- Chat header with back button and user name -->
        <div class="chat-header">
            <button class="back-button" onclick="window.history.back()">← Back</button>
            <?= htmlspecialchars($user['name']) ?>
        </div>

        <!-- Chat messages area -->
        <div class="chat-messages" id="chat-messages"></div>

        <!-- Chat footer with input field and send button -->
        <div class="chat-footer">
            <form id="chat-form" style="display: flex; width: 100%;">
                <input type="text" id="message" class="message-input" placeholder="Type a message..." required>
                <button type="submit" class="send-button">Send</button>
            </form>
        </div>
    </div>

    <script>
        // Reference to the chat messages container
        const chatBox = document.getElementById('chat-messages');

        // Function to fetch messages from the server
        function fetchMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_messages.php?user_id=<?= $chat_user_id ?>", true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    chatBox.innerHTML = xhr.responseText; // Update chat messages
                    chatBox.scrollTop = chatBox.scrollHeight; // Scroll to the bottom
                }
            };
            xhr.send();
        }

        // Event listener for the chat form submission
        document.getElementById("chat-form").addEventListener("submit", function(e) {
            e.preventDefault(); // Prevent default form submission
            const messageInput = document.getElementById("message");
            const message = messageInput.value.trim();
            if (message === "") return; // Do nothing if the message is empty

            // Send the message to the server
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    messageInput.value = ""; // Clear the input field
                    fetchMessages(); // Refresh the chat messages
                }
            };
            xhr.send("message=" + encodeURIComponent(message) + "&receiver_id=<?= $chat_user_id ?>");
        });

        // Start polling for new messages every 2 seconds
        setInterval(fetchMessages, 2000);
        window.onload = fetchMessages; // Fetch messages when the page loads
    </script>

</body>

</html>