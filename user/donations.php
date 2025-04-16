<?php
// Include database connection
include '../common/connection.php';
session_start();

// Get the logged-in user's ID from the session
$logged_in_user_id = $_SESSION['user_id'];

// Fetch donation records along with request details
$query = "
    SELECT dn.donor_notifications_id, dn.recipient_id, dn.request_id, dn.donor_notifications_status, 
           br.place, br.hospital_name, br.request_status, br.request_units
    FROM donor_notifications dn
    JOIN blood_requests br ON dn.request_id = br.request_id
    WHERE dn.donor_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<link rel="stylesheet" href="styles/styles.css"> <!-- External CSS -->

<div class="container">
    <div class="header">
        <h2>My Donations</h2>
        <a href="?page=create_donations.php" class="create-btn">+ Create Donation</a> <!-- Button to create a new request -->
    </div>

    <!-- Table to display donation records -->
    <table>
        <thead>
            <tr>
                <th>Hospital</th> <!-- Hospital name -->
                <th>Request Location</th> <!-- Location of the request -->
                <th>Status</th> <!-- Status of the donation -->
                <th>Actions</th> <!-- Actions available for the user -->
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php
                // Loop through each donation record
                while ($row = $result->fetch_assoc()):
                    $hideButtons = true;

                    // Count the number of successful donations for this request
                    $donatedStmt = $conn->prepare("
                        SELECT COUNT(*) AS donated_count 
                        FROM donor_notifications 
                        WHERE request_id = ? AND donor_notifications_status = 5
                    ");
                    $donatedStmt->bind_param("i", $row['request_id']);
                    $donatedStmt->execute();
                    $donatedCount = $donatedStmt->get_result()->fetch_assoc()['donated_count'];
                    $donatedStmt->close();

                    // Check if enough donations are received
                    if (!in_array($row['request_status'], [4, 5, 6]) && $donatedCount < $row['request_units']) {
                        $hideButtons = false;
                    }

                    // Check if the current user has already donated
                    $userHasDonated = $row['donor_notifications_status'] == 5;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['hospital_name']) ?></td> <!-- Display hospital name -->
                        <td><?= htmlspecialchars($row['place']) ?></td> <!-- Display request location -->
                        <td>
                            <span class="status status-<?= $row['donor_notifications_status'] ?>">
                                <?= ["Pending", "Accepted", "Rejected", "Opened", "Deleted", "Donated", "Pending"][$row['donor_notifications_status']] ?>
                            </span> <!-- Display donation status -->
                        </td>
                        <td>
                            <!-- Details button always visible -->
                            <a href="?page=details_donations.php?request_id=<?= $row['request_id'] ?>" class="btn btn-details">Details</a>

                            <!-- Show action buttons based on conditions -->
                            <?php if (!$hideButtons || $userHasDonated): ?>
                                <?php if ($row['donor_notifications_status'] == 0 || $row['donor_notifications_status'] == 6): ?>
                                    <!-- Accept and Reject buttons -->
                                    <button class="btn btn-accept" onclick="updateStatus(<?= $row['donor_notifications_id'] ?>, 1)">Accept</button>
                                    <button class="btn btn-reject" onclick="updateStatus(<?= $row['donor_notifications_id'] ?>, 2)">Reject</button>
                                <?php elseif ($row['donor_notifications_status'] == 1 || $row['donor_notifications_status'] == 5): ?>
                                    <!-- Chat button -->
                                    <a href="chat.php?user_id=<?= $row['recipient_id'] ?>" class="btn btn-chat">Chat</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- Display message if enough donations are received -->
                                <span style="color: green; font-weight: bold;">Enough donations</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Display message if no donations are found -->
                <tr>
                    <td colspan="4" style="text-align:center;">No donations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Function to update the status of a donation
    function updateStatus(notificationId, status) {
        let xhr = new XMLHttpRequest(); // Create a new XMLHttpRequest object
        xhr.open("POST", "update_donation_status.php", true); // Open a POST request
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); // Set the request header
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                location.reload(); // Refresh the page after the status is updated
            }
        };
        // Send the notification ID and status to the server
        xhr.send("notification_id=" + notificationId + "&status=" + status);
    }
</script>