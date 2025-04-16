<?php
// Include the database connection file
include '../common/connection.php';

// Start the session (if not already started)
session_start();

// Check if the user is logged in as an admin (optional, add your own authentication logic here)
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: login.php'); // Redirect to login page
//     exit();
// }

// Fetch all enquiries from the database
$sql = "SELECT * FROM enquiry ORDER BY enquiry_time DESC";
$result = $conn->query($sql);

function getStatusLabel($status)
{
    return $status == 1 ? 'Completed' : 'Processing';
}

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
    <title>Enquiry List</title>
    <style>
        .btn-block {
            background-color: rgb(53, 220, 95);
            color: #fff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #ffc8c8;
            max-width: 100%;
            overflow-x: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .status-processing {
            background-color: #ffe0e0;
            color: #d32f2f;
            font-weight: bold;
        }

        .status-completed {
            background-color: #e0ffe0;
            color: #4caf50;
            font-weight: bold;
        }

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
        <h1>Enquiry List</h1>
        <table style='color:black'>
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
                <?php if ($result->num_rows > 0) : $i = 1; ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?php echo htmlspecialchars($row['enquirer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['enquirer_email']); ?></td>
                            <td style='max-width:350px;word-wrap: break-word;white-space: normal;'><?php echo htmlspecialchars($row['enquirer_message']); ?></td>
                            <td class="<?php echo getStatusClass($row['enquiry_status']); ?>">
                                <?php echo getStatusLabel($row['enquiry_status']); ?>
                            </td>
                            <td><?php echo date("d-m-Y g:i A", strtotime($row['enquiry_time'])); ?></td>
                            <td>
                                <a class="btn-block" href="process_enquiry.php?id=<?php echo $row['enquiry_id']; ?>">Process</a>
                                <!-- <a href="delete_enquiry.php?id=<?php echo $row['enquiry_id']; ?>">Delete</a> -->
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
// Close the database connection
$conn->close();
?>