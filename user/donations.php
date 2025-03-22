<?php
// Include database connection
include '../common/connection.php';

session_start();
$logged_in_user_id = $_SESSION['user_id'];

// Fetch donations made by the logged-in donor
$query = "SELECT dn.donor_notifications_id, dn.recipient_id, dn.request_id, dn.donor_notifications_status, br.place, br.hospital_name
          FROM donor_notifications dn
          JOIN blood_requests br ON dn.request_id = br.request_id
          WHERE dn.donor_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $logged_in_user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<link rel="stylesheet" href="styles.css"> <!-- External CSS -->

<div class="container">
    <div class="header">
        <h2>My Donations</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Hospital</th>
                <th>Request Location</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['hospital_name']) ?></td>
                        <td><?= htmlspecialchars($row['place']) ?></td>
                        <td>
                            <span class="status status-<?= $row['donor_notifications_status'] ?>">
                                <?= ["Created", "Accepted", "Rejected", "Opened", "Deleted", "Donated"][$row['donor_notifications_status']] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['donor_notifications_status'] == 0): ?>
                                <button class="btn btn-accept" onclick="updateStatus(<?= $row['donor_notifications_id'] ?>, 1)">Accept</button>
                                <button class="btn btn-reject" onclick="updateStatus(<?= $row['donor_notifications_id'] ?>, 2)">Reject</button>
                            <?php elseif ($row['donor_notifications_status'] == 1): ?>
                                <a href="chat.php?user_id=<?= $row['recipient_id'] ?>" class="btn btn-chat">Chat</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;">No donations found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function updateStatus(notificationId, status) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "update_donation_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                location.reload(); // Refresh page after status update
            }
        };
        xhr.send("notification_id=" + notificationId + "&status=" + status);
    }
</script>

<style>
    /* Status Colors */
    .status {
        font-weight: bold;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
    }

    .status-0 {
        background: orange;
        color: white;
    }

    /* Created */
    .status-1 {
        background: green;
        color: white;
    }

    /* Accepted */
    .status-2 {
        background: red;
        color: white;
    }

    /* Rejected */
    .status-3 {
        background: blue;
        color: white;
    }

    /* Opened */
    .status-4 {
        background: gray;
        color: white;
    }

    /* Deleted */
    .status-5 {
        background: purple;
        color: white;
    }

    /* Donated */

    /* Buttons */
    .btn {
        padding: 8px 12px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
        margin-right: 5px;
    }

    .btn-accept {
        background: green;
        color: white;
    }

    .btn-reject {
        background: red;
        color: white;
    }

    .btn-chat {
        background: blue;
        color: white;
    }

    .btn:hover {
        opacity: 0.8;
    }
</style>