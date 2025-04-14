<?php
include '../common/connection.php'; // Include database connection

// Get the request ID from the URL parameter and validate it
$request_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($request_id <= 0) {
    // Redirect to the requests page if the request ID is invalid
    echo "<script>alert('Invalid request!'); window.location.href='?page=requests.php';</script>";
    exit();
}

// Fetch blood request details
$query = "SELECT br.*, u.name, u.email, u.phone FROM blood_requests br
          JOIN users u ON br.recipient_id = u.user_id
          WHERE br.request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id); // Bind the request ID as an integer
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc(); // Fetch the request details

if (!$request) {
    // Redirect to the requests page if the request is not found
    echo "<script>alert('Request not found!'); window.location.href='?page=requests.php';</script>";
    exit();
}

// Fetch accepted and donated donors
$query = "SELECT dn.donor_id, u.name, u.email, u.phone, dn.donor_notifications_status FROM donor_notifications dn
          JOIN users u ON dn.donor_id = u.user_id
          WHERE dn.request_id = ? AND dn.donor_notifications_status IN (1, 5)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id); // Bind the request ID as an integer
$stmt->execute();
$donors = $stmt->get_result(); // Fetch the donors' details

$accepted_count = 0; // Initialize the count of accepted donors
$donated_count = 0; // Initialize the count of donated donors
$accepted_donors = []; // Array to store accepted donors

// Loop through the donors and categorize them based on their status
while ($donor = $donors->fetch_assoc()) {
    if ($donor['donor_notifications_status'] == 1) {
        $accepted_count++; // Increment the count of accepted donors
    } elseif ($donor['donor_notifications_status'] == 5) {
        $donated_count++; // Increment the count of donated donors
    }
    $accepted_donors[] = $donor; // Add the donor to the accepted donors array
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Blood Request</title>
    <link rel="stylesheet" href="styles/styles.css"> <!-- Link to external stylesheet -->
</head>

<body>

    <div class="container">
        <h2>Blood Request Details</h2>
        <!-- Table to display blood request details -->
        <table class="details-table">
            <tr>
                <th>Request ID</th>
                <td><?php echo $request['request_id']; ?></td> <!-- Display request ID -->
            </tr>
            <tr>
                <th>Recipient Name</th>
                <td><?php echo $request['name']; ?></td> <!-- Display recipient name -->
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $request['email']; ?></td> <!-- Display recipient email -->
            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo $request['phone']; ?></td> <!-- Display recipient phone -->
            </tr>
            <tr>
                <th>Blood Group</th>
                <td><?php echo $request['blood_group']; ?></td> <!-- Display blood group -->
            </tr>
            <tr>
                <th>Units Needed</th>
                <td><?php echo $request['request_units']; ?></td> <!-- Display units needed -->
            </tr>
            <tr>
                <th>Hospital</th>
                <td><?php echo $request['hospital_name']; ?></td> <!-- Display hospital name -->
            </tr>
            <tr>
                <th>Doctor Name</th>
                <td><?php echo $request['doctor_name']; ?></td> <!-- Display doctor name -->
            </tr>
            <tr>
                <th>Additional Notes</th>
                <td><?php echo $request['additional_notes']; ?></td> <!-- Display additional notes -->
            </tr>
            <tr>
                <th>Location</th>
                <td><?php echo $request['place']; ?></td> <!-- Display location -->
            </tr>
            <tr>
                <th>Latitude</th>
                <td><?php echo $request['latitude']; ?></td> <!-- Display latitude -->
            </tr>
            <tr>
                <th>Longitude</th>
                <td><?php echo $request['longitude']; ?></td> <!-- Display longitude -->
            </tr>
        </table>

        <h3>Donor Details</h3>
        <!-- Summary of donor details -->
        <p class="summary">Accepted: <?php echo $accepted_count; ?> | Donated: <?php echo $donated_count; ?> / Required: <?php echo $request['request_units']; ?></p>

        <!-- Table to display donor details -->
        <table class="donors-table">
            <tr>
                <th>Name</th> <!-- Column for donor name -->
                <th>Email</th> <!-- Column for donor email -->
                <th>Phone</th> <!-- Column for donor phone -->
                <th>Status</th> <!-- Column for donor status -->
                <th>Action</th> <!-- Column for actions -->
            </tr>
            <?php foreach ($accepted_donors as $donor): ?>
                <tr>
                    <td><?php echo $donor['name']; ?></td> <!-- Display donor name -->
                    <td><?php echo $donor['email']; ?></td> <!-- Display donor email -->
                    <td><?php echo $donor['phone']; ?></td> <!-- Display donor phone -->
                    <td><?php echo ($donor['donor_notifications_status'] == 1) ? 'Accepted' : 'Donated'; ?></td> <!-- Display donor status -->
                    <td><a href="chat.php?user_id=<?php echo $donor['donor_id'];  ?>" class="btn-chat">Chat</a> <!-- Link to chat with donor -->
                        <?php
                        // Assuming $request_id is already available
                        $donatedQuery = "SELECT COUNT(*) AS donated_count FROM donor_notifications WHERE request_id = $request_id AND donor_notifications_status = 5";
                        $donatedResult = $conn->query($donatedQuery);
                        $donatedRow = $donatedResult->fetch_assoc();
                        $donated_count = $donatedRow['donated_count'];

                        $required_units = $request['request_units']; // assuming this is part of the $request
                        ?>
                        <?php if ($donor['donor_notifications_status'] == 1 && $donated_count < $required_units): ?>
                            <a href="mark_donated.php?donor_id=<?php echo $donor['donor_id']; ?>&request_id=<?php echo $request['request_id']; ?>"
                                class="btn-donated"
                                onclick="return confirm('Mark this donor as Donated?');">Donated</a>
                        <?php endif; ?>

                    </td>
                </tr>

                <?php if (isset($_SESSION['donation_error'])): ?>
                    <script>
                        alert('<?php echo $_SESSION['donation_error']; ?>');
                    </script>
                    <?php unset($_SESSION['donation_error']); ?>
                <?php endif; ?>

            <?php endforeach; ?>
        </table>
    </div>

</body>
<style>
    .btn-donated {
        display: inline-block;
        background-color: #28a745;
        /* green */
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-donated:hover {
        background-color: #218838;
        /* darker green */
        transform: scale(1.05);
    }

    .btn-donated:active {
        transform: scale(0.98);
    }

    .btn-chat {
        /* Styling for the chat button */
        background-color: #1976D2;
        color: white;
        padding: 5px 10px;
        text-decoration: none;
        border-radius: 5px;
    }
</style>

</html>