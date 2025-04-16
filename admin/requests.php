<?php
include '../common/connection.php';
include '../common/session_check_admin.php';

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
    <link rel="stylesheet" href="adminstyles/requests.css?t=<?= time() ?>">
    <style>
        .table-container {
            margin: auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }
    </style>
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody style='color:black'>
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
                                case 1:
                                    echo "<span class='urgency low'>Low</span>";
                                    break;
                                case 2:
                                    echo "<span class='urgency medium'>Medium</span>";
                                    break;
                                case 3:
                                    echo "<span class='urgency high'>High</span>";
                                    break;
                                default:
                                    echo "<span class='urgency unknown'>Unknown</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            switch ($row['request_status']) {
                                case 1:
                                    echo "<span class='status pending'>Pending</span>";
                                    break;
                                case 3:
                                    echo "<span class='status pending'>Pending</span>";
                                    break;
                                case 4:
                                    echo "<span class='status fulfilled'>Fulfilled</span>";
                                    break;
                                case 6:
                                    echo "<span class='status cancelled'>Cancelled</span>";
                                    break;
                                case 5:
                                    echo "<span class='status Blocked'>Blocked</span>";
                                    break;
                                default:
                                    echo "<span class='status unknown'>Unknown</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            echo "<form method='POST' action='blockRequest.php'>
                                    <input type='hidden' name='requestID' value='{$row['request_id']}'>";
                            echo "<input type='hidden' name='Status' value='" . (($row['request_status'] == "5") ? "3" : "5") . "'><div class='actionButtonsCollection'>";
                            echo ($row['request_status'] == 5) ? "<button class='btn-block UnBlock'>UnBlock</button>" : "";
                            echo ($row['request_status'] != 5) ? "<button class='btn-block Block'>Block</button>" : "";
                            echo "</div></form>";
                            ?>

                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="requests.js?v=<?= time() ?>"></script>
    <script>
        if (window.initRequestsPage) {
            window.initRequestsPage();
        }
    </script>

</body>

</html>