<?php
// Start session and include database connection
session_start();
include '../common/connection.php';

// Define status labels for request statuses
$statusLabels = [
    1 => "Request Created", // Status when the request is newly created
    3 => "Pending", // Status when the request is awaiting action
    4 => "Successfully Closed", // Status when the request is completed successfully
    5 => "Blocked by Admin", // Status when the request is blocked by an admin
    6 => "Closed Request" // Status when the request is manually closed
];

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in."); // Terminate if the user is not logged in
}

// Display an alert if the maximum request level is reached
if (isset($_GET['msg']) && $_GET['msg'] == 'maxlevel') {
    echo "<script>alert('This is the maximum request level.');</script>";
}

?>
<script>
    // Check if the URL contains the 'msg=maxlevel' parameter
    if (window.location.search.includes('msg=maxlevel')) {
        alert('This is the maximum request level.'); // Display an alert
        // Remove the query string from the URL
        window.history.replaceState(null, null, window.location.pathname);
    }
</script>
<?php

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

// Check connection to the database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Terminate if the connection fails
}

// Fetch requests from the database for the logged-in user
$sql = "SELECT request_id, blood_group, request_units, hospital_name, doctor_name, request_status, request_time 
        FROM blood_requests 
        WHERE recipient_id = ? 
        ORDER BY request_time DESC"; // Order requests by the most recent first

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind user ID as an integer
$stmt->execute();
$result = $stmt->get_result(); // Execute the query and get the result
?>

<link rel="stylesheet" href="styles/styles.css"> <!-- Link to external CSS -->

<div class="container">
    <div class="header">
        <h2>My Blood Requests</h2> <!-- Page header -->
        <a href="?page=create_requests.php" class="create-btn">+ Create Request</a> <!-- Button to create a new request -->
    </div>

    <!-- Table to display blood requests -->
    <table>
        <thead>
            <tr>
                <th>Blood Group</th> <!-- Column for blood group -->
                <th>Units</th> <!-- Column for units required -->
                <th>Hospital</th> <!-- Column for hospital name -->
                <th>Doctor</th> <!-- Column for doctor name -->
                <th>Status</th> <!-- Column for request status -->
                <th>Requested On</th> <!-- Column for request creation date -->
                <th>Actions</th> <!-- Column for actions (view/edit) -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if there are any requests
            if ($result->num_rows > 0) {
                // Loop through each request and display it in the table
                while ($row = $result->fetch_assoc()) {
                    $statusText = $statusLabels[$row['request_status']] ?? "Unknown"; // Get the status label
                    echo "<tr>
                            <td>{$row['blood_group']}</td> <!-- Display blood group -->
                            <td>{$row['request_units']}</td> <!-- Display units required -->
                            <td>{$row['hospital_name']}</td> <!-- Display hospital name -->
                            <td>{$row['doctor_name']}</td> <!-- Display doctor name -->
                            <td>{$statusText}</td> <!-- Display request status -->
                            <td>" . date("M d, Y h:i A", strtotime($row['request_time'])) . "</td> <!-- Display request time -->
                            <td class='actions'>
                                <a href='?page=view_requests.php?id={$row['request_id']}' class='view-btn'>View</a> <!-- Link to view request -->
                                <a href='?page=edit_requests.php?request_id={$row['request_id']}' class='edit-btn'>Edit</a> <!-- Link to edit request -->
                            </td>
                          </tr>";
                }
            } else {
                // Display a message if no requests are found
                echo "<tr><td colspan='7' style='text-align:center;'>No requests found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
// Close the prepared statement and database connection
$stmt->close();
$conn->close();
?>