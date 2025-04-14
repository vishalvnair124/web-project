<?php
// Start session and include database connection
session_start();
include '../common/connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recipient_id = $_SESSION['user_id'];
    $blood_group = $_POST['blood_group'];
    $request_units = (int)$_POST['request_units'];
    $when_need_blood = $_POST['when_need_blood'];
    $hospital_name = $_POST['hospital_name'];
    $doctor_name = $_POST['doctor_name'];
    $additional_notes = $_POST['additional_notes'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $place = $_POST['place'] ?? 'Unknown Location';

    $request_level = 1;
    $request_status = 1;

    // ✅ Correct SQL statement (12 placeholders for 12 values)
    $stmt = $conn->prepare("INSERT INTO blood_requests 
        (recipient_id, blood_group, request_units, when_need_blood, hospital_name, doctor_name, additional_notes, latitude, longitude, place, request_level, request_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // ✅ Correct bind_param() with 12 values
    $stmt->bind_param(
        "isisssssssis", // Fix: added missing `s` for `place`
        $recipient_id,
        $blood_group,
        $request_units,
        $when_need_blood,
        $hospital_name,
        $doctor_name,
        $additional_notes,
        $latitude,
        $longitude,
        $place,          // Fix: Ensure `place` is correctly mapped
        $request_level,
        $request_status
    );

    if ($stmt->execute()) {
        // Redirect after successful submission
        //  Get the last inserted blood_request_id
        $blood_request_id = $stmt->insert_id;

        header("Location: ../search/create_notification.php?request_id=" . $blood_request_id);
        exit();
    } else {
        echo "Error: " . $stmt->error;
        header("Location: ../user/?page=requests.php");
    }

    $stmt->close();
}

$conn->close();
