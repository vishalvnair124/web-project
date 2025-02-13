<?php
include '../common/connection.php';

session_start();

if (isset($_GET['id'])) {
    $enquiry_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM enquiry WHERE enquiry_id = ?");
    $stmt->bind_param("i", $enquiry_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $enquiry = $result->fetch_assoc();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $status = 1;
            $custom_message = trim($_POST['message']);

            $update_stmt = $conn->prepare("UPDATE enquiry SET enquiry_status = ? WHERE enquiry_id = ?");
            $update_stmt->bind_param("ii", $status, $enquiry_id);
            $update_stmt->execute();
            $update_stmt->close();

            $to = $enquiry['enquirer_email'];
            $subject = "Your Enquiry Has Been Processed";
            $message = "Dear " . $enquiry['enquirer_name'] . ",\n\n" . $custom_message . "\n\nBest regards,\nYour Company";
            $headers = "From: yourcompany@example.com";

            if (mail($to, $subject, $message, $headers)) {
                $email_status = 'Email sent successfully.';
            } else {
                $email_status = 'Failed to send email.';
            }

            $alert_type = 'success';
            $alert_message = "Enquiry processed successfully. $email_status";

            header("Location: enquiry.php?type=$alert_type&message=" . urlencode($alert_message));
            exit();
        }
    } else {
        $alert_type = 'error';
        $alert_message = "Enquiry not found.";
        header("Location: enquiry.php?type=$alert_type&message=" . urlencode($alert_message));
        exit();
    }

    $stmt->close();
} else {
    $alert_type = 'error';
    $alert_message = "Invalid enquiry ID.";
    header("Location: enquiry.php?type=$alert_type&message=" . urlencode($alert_message));
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Enquiry</title>
    <style>
        :root {
            --background-color1: #ffeaea;
            /* Light red background */
            --background-color2: #ffffff;
            /* White for contrast */
            --background-color3: #ffe0e0;
            /* Soft red for input fields */
            --background-color4: #ffc8c8;
            /* Light red */
            --primary-color: #d32f2f;
            /* Dark red for primary elements */
            --secondary-color: #b71c1c;
            /* Deeper red for secondary elements */
            --border-color: #c62828;
            /* Red for borders */
            --one-use-color: #e53935;
            /* Red for specific elements */
            --two-use-color: #f44336;
            /* Bright red for specific elements */
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--background-color1);
            color: var(--secondary-color);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: var(--background-color2);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--border-color);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        p {
            font-size: 16px;
            margin: 10px 0;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: var(--secondary-color);
        }

        textarea {
            width: 90%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            resize: vertical;
            font-size: 14px;
            line-height: 1.5;
            background-color: var(--background-color3);
            color: var(--secondary-color);
        }

        input[type="submit"] {
            background-color: var(--primary-color);
            color: var(--background-color2);
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Process Enquiry</h1>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($enquiry['enquirer_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($enquiry['enquirer_email']); ?></p>
        <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($enquiry['enquirer_message'])); ?></p>

        <form method="post" action="">
            <label for="message">Custom Message to Enquirer:</label>
            <textarea name="message" id="message" rows="6" required></textarea>
            <input type="submit" value="Send Message and Mark as Processed">
        </form>
    </div>
</body>

</html>