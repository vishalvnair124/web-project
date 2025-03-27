<?php
// Include database connection
include '../common/connection.php';

// Start the session to access session variables
session_start();
$logged_in_user_id = $_SESSION['user_id']; // Get the logged-in user's ID from the session

// Fetch users the logged-in user has chatted with
$query = "SELECT DISTINCT u.user_id, u.name 
          FROM users u 
          JOIN messages m ON (u.user_id = m.sender_id OR u.user_id = m.receiver_id) 
          WHERE (m.sender_id = $logged_in_user_id OR m.receiver_id = $logged_in_user_id) 
          AND u.user_id != $logged_in_user_id"; // Exclude the logged-in user from the list

$result = $conn->query($query); // Execute the query to fetch chat users

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chats</title>
    <link rel="stylesheet" href="styles/styles.css"> <!-- Link to external CSS -->

    <style>
        /* Basic styling for the chat list interface */
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
            /* Primary color for the header */
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .chat-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .chat-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            /* Separator between chat items */
            cursor: pointer;
            transition: background 0.3s;
        }

        .chat-item:hover {
            background: #ffc8c8;
            /* Highlight chat item on hover */
        }

        .chat-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            /* Circular avatar */
            background: var(--primary-color);
            /* Primary color for avatar */
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            font-weight: bold;
            margin-right: 15px;
        }

        .chat-name {
            flex-grow: 1;
            /* Take up remaining space */
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .goto-chat {
            background: var(--primary-color);
            /* Button background color */
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .goto-chat:hover {
            background: #c62828;
            /* Darker shade on hover */
        }
    </style>
</head>

<body>

    <div class="chat-container">
        <!-- Header for the chat list -->
        <div class="chat-header">Chats</div>

        <!-- List of users the logged-in user has chatted with -->
        <ul class="chat-list">
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="chat-item">
                    <!-- Display the first letter of the user's name as an avatar -->
                    <div class="chat-avatar">
                        <?= strtoupper(substr($row['name'], 0, 1)) ?> <!-- First letter of name -->
                    </div>
                    <!-- Display the user's name -->
                    <div class="chat-name"><?= htmlspecialchars($row['name']) ?></div>
                    <!-- Link to the chat page for the selected user -->
                    <a href="chat.php?user_id=<?= $row['user_id'] ?>" class="goto-chat">Go to Chat</a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

</body>

</html>