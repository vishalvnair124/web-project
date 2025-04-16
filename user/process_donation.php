<?php
// Start session and include necessary files
session_start();
include '../common/connection.php';
include '../common/session_check.php'; // Ensure user is logged in

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestid = $_POST['requestid'];
    $donor_id = $_SESSION['user_id']; // Get the donor ID from session
    $donor_email = $_SESSION['user_email']; // Get the donor email from session
    $request_id = intval($requestid); // Ensure request ID is an integer

    $donor_notifications_status = 1; // Status for "Pending"

    // Check if the request ID exists in the database
    $check_sql = "SELECT * FROM blood_requests WHERE request_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();



    if ($result->num_rows > 0) {
        // Request ID is valid, proceed to check if the donor has same boold group and user avaliabilty status should be 1
        $request = $result->fetch_assoc();  // Fetch the request details
        $blood_group = $request['blood_group'];
        $recipient_id = $request['recipient_id']; // Get the recipient ID from the request
        // echo $blood_group; // Display the blood group for debugging
        // echo $donor_email; // Display the donor email for debugging
        // echo $request_id; // Display the request ID for debugging

        if ($request['request_status'] == 5 || $request['request_status'] == 6 || $request['request_status'] == 4) {
            // Check if the request is already accepted or completed
            $error = 6;
            header("Location: http://localhost/dropforlife/user/?page=create_donations.php?error-message=$error");
            exit();
        }

        $get_sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($get_sql);
        $stmt->bind_param("s", $_SESSION['user_email']);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $donor = $get_result->fetch_assoc(); // Fetch the donor details

        $donor_blood_group = $donor['blood_group'];
        $user_availability_status = $donor['availability_status']; // Get the donor's availability status
        $donor_id = $donor['user_id']; // Get the donor ID from the user details

        if ($recipient_id == $donor_id) {
            // Check if the donor is trying to donate to their own request
            echo $donor_id . '<br>'; // Display the donor's user ID for debugging
            echo $recipient_id; // Display the donor ID for debugging
            $error = 1;
            header("Location: http://localhost/dropforlife/user/?page=create_donations.php?error-message=$error");

            exit();
        }
        if ($blood_group != $donor_blood_group) {
            $error = 2;
            header("Location: http://localhost/dropforlife/user/?page=create_donations.php?error-message=$error");

            exit();
        } else if ($user_availability_status != 1) {
            // Check if the donor is available to donate blood
            $error = 3;
            header("Location: http://localhost/dropforlife/user/?page=create_donations.php?error-message=$error");
            exit();
        }

        // Check if the donor has already been notified for this request
        $check_donor_sql = "SELECT * FROM donor_notifications WHERE donor_id = ? AND request_id = ?";
        $stmt = $conn->prepare($check_donor_sql);
        $stmt->bind_param("ii", $donor_id, $request_id);
        $stmt->execute();
        $donor_result = $stmt->get_result();

        if ($donor_result->num_rows == 0) {
            // Donor has not been notified, proceed to insert
            $insert_sql = "INSERT INTO donor_notifications (recipient_id, donor_id,  request_id, donor_notifications_status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iiii", $recipient_id, $donor_id, $request_id, $donor_notifications_status);

            if ($stmt->execute()) {
                // Redirect to the view requests page with success message
                header("Location: http://localhost/dropforlife/user/?page=donations.php");
                exit();
            } else {
                // Error occurred during insertion
                $error = 7;
                header("Location: http://localhost/dropforlife/user/?page=create_donations.php?error-message=$error");
                echo "Error: " . $stmt->error; // Display error message
            }
        } else {
            // Donor has already been notified for this request
            $error = 4;
            header("Location: http://localhost/dropforlife/user/?page=create_donations.php?error-message=$error"); // Redirect to create donations page with error message
        }
    } else {
        // Request ID is invalid, redirect with error message
        $error = 5;
        header("Location: http://localhost/dropforlife/user/?page=create_donations.php?error-message=$error"); // Redirect to create donations page with error message
        exit();
    }

    $stmt->close(); // Close the statement
}
