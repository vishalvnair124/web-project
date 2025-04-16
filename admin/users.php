<?php include '../common/session_check_admin.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Registered Users - Admin</title>
  <link rel="stylesheet" href="adminstyles/users.css?v=<?= time() ?>">
  <style>
    #loading {
      text-align: center;
      padding: 10px;
      display: none;
    }
  </style>
</head>

<body>
  <div class="table-container">
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
        <tbody id="userData" style="color:black">
        </tbody>
      </table>
      <div id="loading">Loading more users...</div>
      <div id="sentinel"></div>
    </div>
  </div>

  <script src="users.js?v=<?= time() ?>"></script>
  <script>
    window.initUsersPage();
  </script>
</body>

</html>