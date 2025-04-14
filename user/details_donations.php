<?php
// Include the database connection file
include '../common/connection.php';

// Start the session to access session variables
session_start();

// Get the logged-in user's ID from the session
$logged_in_user_id = $_SESSION['user_id'];

// Get the request ID from the URL parameter, default to 0 if not provided
$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

// Verify the logged-in donor has access to this request
$checkQuery = "SELECT * FROM donor_notifications WHERE donor_id = ? AND request_id = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("ii", $logged_in_user_id, $request_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

// If no matching record is found, deny access
if ($checkResult->num_rows === 0) {
    echo "<p style='text-align:center; color: red;'>Access Denied. You are not authorized to view this request.</p>";
    exit;
}

// Fetch blood request details from the database
$requestQuery = "SELECT * FROM blood_requests WHERE request_id = ?";
$requestStmt = $conn->prepare($requestQuery);
$requestStmt->bind_param("i", $request_id);
$requestStmt->execute();
$requestResult = $requestStmt->get_result();

// If no details are found for the request, display an error message
if ($requestResult->num_rows === 0) {
    echo "<p style='text-align:center; color: red;'>No details found for this request.</p>";
    exit;
}

// Fetch the request details as an associative array
$request = $requestResult->fetch_assoc();

// Close the database connection
$conn->close();
?>

<!-- Include external stylesheet -->
<link rel="stylesheet" href="styles/styles.css">

<div class="container">
    <div class="header">
        <h2>Donation Request Details</h2>
    </div>

    <!-- Display the blood request details in a table -->
    <table>
        <tr>
            <th>Hospital Name:</th>
            <td><?= htmlspecialchars($request['hospital_name']) ?></td> <!-- Display hospital name -->
        </tr>
        <tr>
            <th>Doctor Name:</th>
            <td><?= htmlspecialchars($request['doctor_name']) ?></td> <!-- Display doctor name -->
        </tr>
        <tr>
            <th>Blood Group:</th>
            <td><?= htmlspecialchars($request['blood_group']) ?></td> <!-- Display blood group -->
        </tr>
        <tr>
            <th>Units Needed:</th>
            <td><?= htmlspecialchars($request['request_units']) ?></td> <!-- Display units needed -->
        </tr>
        <tr>
            <th>Need By:</th>
            <td>
                <?= date("F j, Y g:i A", strtotime($request['when_need_blood'])) ?> <!-- Display the date and time when blood is needed -->
            </td>
        </tr>
        <tr>
            <th>Additional Notes:</th>
            <td><?= nl2br(htmlspecialchars($request['additional_notes'])) ?></td> <!-- Display additional notes -->
        </tr>
        <tr>
            <th>Request Location:</th>
            <td><?= htmlspecialchars($request['place']) ?></td> <!-- Display the location of the request -->
        </tr>
    </table>

    <!-- Display the location on a map -->
    <h3 style="margin-top: 30px;">Location on Map</h3>
    <iframe
        width="100%"
        height="350"
        frameborder="0"
        style="border:0; border-radius:10px; box-shadow: 0 0 10px rgba(0,0,0,0.2); margin-top:10px;"
        src="https://www.google.com/maps?q=<?= $request['latitude'] ?>,<?= $request['longitude'] ?>&hl=es;z=14&output=embed"
        allowfullscreen>
    </iframe>
</div>