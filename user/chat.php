<?php
include '../common/connection.php';
session_start();

$logged_in_user_id = $_SESSION['user_id'];

if (!isset($_GET['user_id'])) {
    die("User not specified.");
}
$chat_user_id = intval($_GET['user_id']);

$user_query = "SELECT name FROM users WHERE user_id = $chat_user_id";
$user_result = $conn->query($user_query);
if ($user_result->num_rows == 0) {
    die("User not found.");
}
$user = $user_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chat with <?= htmlspecialchars($user['name']) ?></title>
    <link rel="stylesheet" href="styles/styles.css">
    <style>
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
            display: flex;
            flex-direction: column;
            height: 80vh;
        }

        .chat-header {
            background: #d32f2f;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            position: relative;
        }

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

        .chat-messages {
            flex-grow: 1;
            overflow-y: auto;
            padding: 15px;
            background: #fff5f5;
            display: flex;
            flex-direction: column;
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
            background: #d32f2f;
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
        <div class="chat-header">
            <button class="back-button" onclick="window.history.back()">← Back</button>
            <?= htmlspecialchars($user['name']) ?>
        </div>

        <div class="chat-messages" id="chat-messages"></div>

        <div class="chat-footer">
            <form id="chat-form" style="display: flex; width: 100%;">
                <input type="text" id="message" class="message-input" placeholder="Type a message..." required>
                <button type="submit" class="send-button">Send</button>
            </form>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chat-messages');

        function fetchMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_messages.php?user_id=<?= $chat_user_id ?>", true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    chatBox.innerHTML = xhr.responseText;
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            };
            xhr.send();
        }

        document.getElementById("chat-form").addEventListener("submit", function(e) {
            e.preventDefault();
            const messageInput = document.getElementById("message");
            const message = messageInput.value.trim();
            if (message === "") return;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    messageInput.value = "";
                    fetchMessages();
                }
            };
            xhr.send("message=" + encodeURIComponent(message) + "&receiver_id=<?= $chat_user_id ?>");
        });

        // Start polling every 2 seconds
        setInterval(fetchMessages, 2000);
        window.onload = fetchMessages;
    </script>

</body>

</html>