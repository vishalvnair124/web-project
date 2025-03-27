<?php
include '../common/connection.php'; // Include database connection

$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($request_id <= 0) {
    echo "<script>alert('Invalid request!'); window.location.href='requests.php';</script>";
    exit();
}

// Fetch blood request details
$query = "SELECT br.*, u.name, u.email, u.phone FROM blood_requests br
          JOIN users u ON br.recipient_id = u.user_id
          WHERE br.request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request) {
    echo "<script>alert('Request not found!'); window.location.href='requests.php';</script>";
    exit();
}

// Fetch accepted and donated donors
$query = "SELECT dn.donor_id, u.name, u.email, u.phone, dn.donor_notifications_status FROM donor_notifications dn
          JOIN users u ON dn.donor_id = u.user_id
          WHERE dn.request_id = ? AND dn.donor_notifications_status IN (1, 5)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id);
$stmt->execute();
$donors = $stmt->get_result();

$accepted_count = 0;
$donated_count = 0;
$accepted_donors = [];

while ($donor = $donors->fetch_assoc()) {
    if ($donor['donor_notifications_status'] == 1) {
        $accepted_count++;
    } elseif ($donor['donor_notifications_status'] == 5) {
        $donated_count++;
    }
    $accepted_donors[] = $donor;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Blood Request</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>

<body>

    <div class="container">
        <h2>Blood Request Details</h2>
        <table class="details-table">
            <tr>
                <th>Request ID:</th>
                <td><?php echo $request['request_id']; ?></td>
            </tr>
            <tr>
                <th>Recipient Name:</th>
                <td><?php echo $request['name']; ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?php echo $request['email']; ?></td>
            </tr>
            <tr>
                <th>Phone:</th>
                <td><?php echo $request['phone']; ?></td>
            </tr>
            <tr>
                <th>Blood Group:</th>
                <td><?php echo $request['blood_group']; ?></td>
            </tr>
            <tr>
                <th>Units Needed:</th>
                <td><?php echo $request['request_units']; ?></td>
            </tr>
            <tr>
                <th>Hospital:</th>
                <td><?php echo $request['hospital_name']; ?></td>
            </tr>
            <tr>
                <th>Doctor Name:</th>
                <td><?php echo $request['doctor_name']; ?></td>
            </tr>
            <tr>
                <th>Additional Notes:</th>
                <td><?php echo $request['additional_notes']; ?></td>
            </tr>
            <tr>
                <th>Location:</th>
                <td><?php echo $request['place']; ?></td>
            </tr>
            <tr>
                <th>Latitude:</th>
                <td><?php echo $request['latitude']; ?></td>
            </tr>
            <tr>
                <th>Longitude:</th>
                <td><?php echo $request['longitude']; ?></td>
            </tr>
        </table>

        <h3>Donor Details</h3>
        <p class="summary">Accepted: <?php echo $accepted_count; ?> | Donated: <?php echo $donated_count; ?> / Required: <?php echo $request['request_units']; ?></p>

        <table class="donors-table">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($accepted_donors as $donor): ?>
                <tr>
                    <td><?php echo $donor['name']; ?></td>
                    <td><?php echo $donor['email']; ?></td>
                    <td><?php echo $donor['phone']; ?></td>
                    <td><?php echo ($donor['donor_notifications_status'] == 1) ? 'Accepted' : 'Donated'; ?></td>
                    <td><a href="chat.php?user_id=<?php echo $donor['donor_id'];  ?>" class="btn-chat">Chat</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
<style>
    .btn-chat {

        color: white;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 5px;
    }
</style>

</html>