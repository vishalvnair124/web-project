<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

// Include database connection
include '../common/connection.php';

$mail = new PHPMailer(true);

try {
    // Configure PHPMailer
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'database71231@gmail.com';
    $mail->Password = 'pgxf ajpr frrt xiqb'; // Use an App Password instead of your real password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('database71231@gmail.com', 'Admin');

    $mail->isHTML(true); // Set email format to HTML
    $mail->SMTPKeepAlive = true; // Keep the connection open

    // Fetch donors with donor_notifications_status = 0
    $query = "SELECT DISTINCT users.email FROM donor_notifications 
              JOIN users ON donor_notifications.donor_id = users.user_id 
              WHERE donor_notifications_status = 0";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }

    // Loop through recipients and send emails
    while ($row = mysqli_fetch_assoc($result)) {
        $recipient = $row['email'];

        $mail->clearAddresses(); // Clear previous recipient
        $mail->addAddress($recipient);
        $mail->Subject = 'Blood Donation Request';
        $mail->Body = "Hello,<br><br>You are requested to donate blood.<br><br>Regards,<br>Admin";

        if ($mail->send()) {
            echo "Email sent to $recipient successfully!<br>";

            // Update donor_notifications_status to 1 after sending email
            $updateQuery = "UPDATE donor_notifications 
                            JOIN users ON donor_notifications.donor_id = users.user_id 
                            SET donor_notifications_status = 6
                            WHERE users.email = '$recipient'";
            mysqli_query($conn, $updateQuery);
        } else {
            echo "Failed to send email to $recipient: " . $mail->ErrorInfo . "<br>";
        }
    }

    $mail->smtpClose();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close database connection
mysqli_close($conn);
