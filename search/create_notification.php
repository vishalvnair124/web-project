<?php
require '../common/connection.php'; // Database connection

// Get request ID from GET request
$request_id = $_GET['request_id'] ?? 0;

if (!$request_id) {
    die("Invalid request ID.");
}

// Fetch recipient ID from the blood_requests table
$query = "SELECT recipient_id FROM blood_requests WHERE request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$recipient = $result->fetch_assoc();
$stmt->close();

if (!$recipient) {
    die("Recipient not found.");
}

$recipient_id = $recipient['recipient_id'];

// Get donors from search.php logic
require 'search.php'; // This will execute and return $donors array

if (empty($donors)) {
    die("No suitable donors found.");
}

// Insert notifications for each donor
$insert_query = "INSERT INTO donor_notifications (recipient_id, donor_id, request_id, donor_notifications_status) 
                 VALUES (?, ?, ?, 0)"; // 0 means 'notification pending'
$stmt = $conn->prepare($insert_query);

foreach ($donors as $donor) {
    $stmt->bind_param("iii", $recipient_id, $donor['user_id'], $request_id);
    $stmt->execute();
}

$stmt->close();
$conn->close();

// Redirect to requests page after success

header("Location: http://localhost/dropforlife/user/?page=requests.php");
exit(); // Prevent further execution
