<?php
include '../common/connection.php';
session_start();

if (isset($_GET['donor_id']) && isset($_GET['request_id'])) {
    $donor_id = intval($_GET['donor_id']);
    $request_id = intval($_GET['request_id']);

    // Get the correct notification_id for this donor and request
    $query = "SELECT donor_notifications_id FROM donor_notifications WHERE donor_id = ? AND request_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $donor_id, $request_id);
    $stmt->execute();
    $stmt->bind_result($notification_id);
    $stmt->fetch();
    $stmt->close();

    if ($notification_id) {
        $update = $conn->prepare("UPDATE donor_notifications SET donor_notifications_status = 5 WHERE donor_notifications_id = ?");
        $update->bind_param("i", $notification_id);
        if ($update->execute()) {
            // Redirect back to the view page
            header("Location: http://localhost/dropforlife/user/?page=view_requests.php?id=" . $request_id);
            exit();
        } else {
            header("Location: http://localhost/dropforlife/user/?page=view_requests.php?id=" . $request_id);
        }
        $update->close();
    } else {
        echo "Notification not found.";
    }
}

$conn->close();
