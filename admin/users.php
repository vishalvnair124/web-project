<?php
include '../common/session_check_admin.php';
include '../common/connection.php'; // ✅ your correct db file

// Fetch users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Users - Admin</title>
    <link rel="stylesheet" href="adminstyles/users.css"> <!-- your external CSS -->
</head>
<body>

<div class="page-header">
    <h2>Registered Users</h2>
</div>

<div class="users-container">
    <table class="users-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Blood Group</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= $row['user_gender'] ?></td>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['phone'] ?></td>
                        <td><?= $row['blood_group'] ?></td>
                        <td>
                            <?php
                                if ($row['user_status'] == 1) {
                                    echo '<span class="badge badge-active">Active</span>';
                                } elseif ($row['user_status'] == 2) {
                                    echo '<span class="badge badge-temp">Temp Block</span>';
                                } else {
                                    echo '<span class="badge badge-blocked">Blocked</span>';
                                }
                            ?>
                        </td>
                        <td class="action-buttons">
                            <form method="post" action="update_user_status.php">
                                <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                                <button class="btn-block" name="block">Block</button>
                                <button class="btn-temp" name="temp">Temp Block</button>
                                <button class="btn-activate" name="activate">Activate</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>