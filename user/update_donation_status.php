<?php
// Include the database connection file
include '../common/connection.php';

// Start the session to access session variables
session_start();

// Check if the request method is POST and required parameters are provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['notification_id']) && isset($_POST['status'])) {
    $notification_id = intval($_POST['notification_id']); // Sanitize and store the notification ID
    $status = intval($_POST['status']); // Sanitize and store the status
    $donor_id = $_SESSION['user_id']; // Get the logged-in donor's ID from the session

    // Update the donor notification status in the database
    $query = "UPDATE donor_notifications SET donor_notifications_status = $status WHERE donor_notifications_id = $notification_id AND donor_id = $donor_id";

    // Execute the query and check if the update was successful
    if ($conn->query($query)) {
        echo "Success"; // Return success message if the update was successful
    } else {
        echo "Error: " . $conn->error; // Return error message if the update failed
    }
}

// Close the database connection
$conn->close();
