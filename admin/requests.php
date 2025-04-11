<?php
include '../common/connection.php';

// Fetch blood requests
$query = "SELECT br.*, u.name AS recipient_name, u.phone, u.blood_group AS recipient_blood_group 
          FROM blood_requests br
          LEFT JOIN users u ON br.recipient_id = u.user_id 
          ORDER BY br.request_time DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blood Requests</title>
    <link rel="stylesheet" href="adminstyles/requests.css">
</head>
<body>
    <div class="table-container">
        <h2>Blood Requests</h2>
        <table class="requests-table">
            <thead>
                <tr>
                    <th>Recipient</th>
                    <th>Phone</th>
                    <th>Blood Group</th>
                    <th>Units</th>
                    <th>Hospital</th>
                    <th>Requested On</th>
                    <th>Urgency</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr class="<?= ($row['request_status'] == 2) ? 'blocked-row' : ''; ?>">
                        <td><?= htmlspecialchars($row['recipient_name']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['blood_group']) ?></td>
                        <td><?= (int)$row['request_units'] ?></td>
                        <td><?= htmlspecialchars($row['hospital_name']) ?: 'N/A' ?></td>
                        <td><?= date('d-m-Y h:i A', strtotime($row['request_time'])) ?></td>
                        <td>
                            <?php
                            switch ($row['request_level']) {
                                case 1: echo "<span class='urgency low'>Low</span>"; break;
                                case 2: echo "<span class='urgency medium'>Medium</span>"; break;
                                case 3: echo "<span class='urgency high'>High</span>"; break;
                                default: echo "<span class='urgency unknown'>Unknown</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            switch ($row['request_status']) {
                                case 0: echo "<span class='status pending'>Pending</span>"; break;
                                case 1: echo "<span class='status fulfilled'>Fulfilled</span>"; break;
                                case 2: echo "<span class='status cancelled'>Cancelled</span>"; break;
                                default: echo "<span class='status unknown'>Unknown</span>";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>