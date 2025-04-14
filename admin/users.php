<?php
include '../common/session_check_admin.php';
include '../common/connection.php';

$sql = "SELECT * FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registered Users - Admin</title>
  <link rel="stylesheet" href="adminstyles/users.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="page-header">
  <h2>Registered Users</h2>
</div>

<div class="popup-message" id="popupMessage"></div>

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
          <tr data-user-id="<?= $row['user_id'] ?>">
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['user_gender'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['blood_group'] ?></td>
            <td class="status-cell">
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
              <button class="btn-block" data-action="block">Block</button>
              <button class="btn-temp" data-action="temp">Temp Block</button>
              <button class="btn-activate" data-action="activate">Activate</button>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7">No users found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
$(document).ready(function() {
  $(".action-buttons button").click(function(e) {
    e.preventDefault();

    const row = $(this).closest("tr");
    const userId = row.data("user-id");
    const action = $(this).data("action");

    $.ajax({
      url: "update_user_status.php",
      method: "POST",
      data: { user_id: userId, [action]: true },
      success: function(response) {
        $("#popupMessage").text(response).fadeIn().delay(2000).fadeOut();

        // Optionally update the status text
        let newStatus = "";
        if (action === "block") newStatus = '<span class="badge badge-blocked">Blocked</span>';
        else if (action === "temp") newStatus = '<span class="badge badge-temp">Temp Block</span>';
        else if (action === "activate") newStatus = '<span class="badge badge-active">Active</span>';

        row.find(".status-cell").html(newStatus);
      },
      error: function() {
        $("#popupMessage").text("Something went wrong.").fadeIn().delay(2000).fadeOut();
      }
    });
  });
});
</script>

</body>
</html>
