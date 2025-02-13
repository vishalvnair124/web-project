<?php
include '../common/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $status = 0;

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO enquiry (enquirer_name, enquirer_email, enquirer_message, enquiry_status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $email, $message, $status);

        if ($stmt->execute()) {
            $alert_type = 'success';
            $alert_message = 'Enquiry submitted successfully!';
        } else {
            $alert_type = 'error';
            $alert_message = 'Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $alert_type = 'error';
        $alert_message = 'All fields are required.';
    }

    $conn->close();

    header("Location: ../index.php?type=$alert_type&message=" . urlencode($alert_message));
    exit();
}
