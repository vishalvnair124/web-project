<?php
include '../common/connection.php';
session_start();

if (isset($_GET['donor_id']) && isset($_GET['request_id'])) {
    $donor_id = intval($_GET['donor_id']);
    $request_id = intval($_GET['request_id']);

    // Step 1: Get how many donors already marked as donated for this request
    $donated_sql = "SELECT COUNT(*) FROM donor_notifications WHERE request_id = ? AND donor_notifications_status = 5";
    $stmt = $conn->prepare($donated_sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->bind_result($donated_count);
    $stmt->fetch();
    $stmt->close();

    // Step 2: Get required units from blood_requests table
    $required_sql = "SELECT request_units FROM blood_requests WHERE request_id = ?";
    $stmt = $conn->prepare($required_sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $stmt->bind_result($required_units);
    $stmt->fetch();
    $stmt->close();

    // Step 3: Compare and update only if donated < required
    if ($donated_count < $required_units) {
        // Get the notification ID
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
            $update->execute();
            $update->close();
        }
    } else {
        // Optional: set an error session or message
        $_SESSION['donation_error'] = "Required units already fulfilled!";
    }

    // Redirect either way
    header("Location: http://localhost/dropforlife/user/?page=view_requests.php?id=" . $request_id);
    exit();
}

$conn->close();
