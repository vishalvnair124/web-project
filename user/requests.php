<?php
// Start session and include database connection
session_start();
include '../common/connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch requests from the database for the logged-in user
$sql = "SELECT request_id, blood_group, request_units, hospital_name, doctor_name, request_status, request_time 
        FROM blood_requests 
        WHERE recipient_id = ? 
        ORDER BY request_time DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind user ID as an integer
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->

<div class="container">
    <div class="header">
        <h2>My Blood Requests</h2>
        <a href="?page=create_requests.php" class="create-btn">+ Create Request</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Blood Group</th>
                <th>Units</th>
                <th>Hospital</th>
                <th>Doctor</th>
                <th>Status</th>
                <th>Requested On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['blood_group']}</td>
                            <td>{$row['request_units']}</td>
                            <td>{$row['hospital_name']}</td>
                            <td>{$row['doctor_name']}</td>
                            <td>{$row['request_status']}</td>
                            <td>{$row['request_time']}</td>
                            <td class='actions'>
                                <a href='?page=view_requests.php&id={$row['request_id']}' class='view-btn'>View</a>
                                <a href='?page=edit_requests.php&id={$row['request_id']}' class='edit-btn'>Edit</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>No requests found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
$stmt->close();
$conn->close();
?>