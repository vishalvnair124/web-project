<?php
// Include the database connection file
include '../common/connection.php';

// Start session for authentication check
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php'); // Redirect to login page
    exit();
}

// Fetch all enquiries from the database
$sql = "SELECT * FROM enquiry ORDER BY enquiry_time DESC";
$result = $conn->query($sql);

// Function to get status label
function getStatusLabel($status)
{
    return $status == 1 ? 'Completed' : 'Processing';
}

// Function to get CSS class based on status
function getStatusClass($status)
{
    return $status == 1 ? 'status-completed' : 'status-processing';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enquiry List - Admin</title>
    <link rel="stylesheet" href="../adminstyles/enquiry.css">
</head>

<body>
    <div class="container">
        <h1>Enquiry List</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date & Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['enquiry_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['enquirer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['enquirer_email']); ?></td>
                            <td><?php echo htmlspecialchars($row['enquirer_message']); ?></td>
                            <td class="<?php echo getStatusClass($row['enquiry_status']); ?>">
                                <?php echo getStatusLabel($row['enquiry_status']); ?>
                            </td>
                            <td><?php echo date("d M Y, h:i A", strtotime($row['enquiry_time'])); ?></td>
                            <td>
                                <?php if ($row['enquiry_status'] == 0) : ?>
                                    <a href="process_enquiry.php?id=<?php echo $row['enquiry_id']; ?>" class="btn-process">Mark as Completed</a>
                                <?php endif; ?>
                                <a href="delete_enquiry.php?id=<?php echo $row['enquiry_id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this enquiry?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">No enquiries found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>
