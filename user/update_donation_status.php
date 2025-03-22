<?php
include '../common/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['notification_id']) && isset($_POST['status'])) {
    $notification_id = intval($_POST['notification_id']);
    $status = intval($_POST['status']);
    $donor_id = $_SESSION['user_id'];

    $query = "UPDATE donor_notifications SET donor_notifications_status = $status WHERE donor_notifications_id = $notification_id AND donor_id = $donor_id";
    if ($conn->query($query)) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
