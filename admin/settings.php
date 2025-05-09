<?php
include '../common/connection.php';
session_start();

// Use session-based ID in production
$admin_id = 1;

$stmt = $conn->prepare("SELECT * FROM admin WHERE Admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Settings</title>
  <link rel="stylesheet" href="adminstyles/settings.css?v=<?= time() ?>">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
</head>

<body>

  <div class="card">
    <h2 style='color:black'>Admin Settings</h2>
    <form id="settingsForm">
      <label>Name:</label>
      <input type="text" name="name" value="<?= htmlspecialchars($admin['Admin_name']) ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($admin['Admin_email']) ?>" required>

      <label>New Password:</label>
      <input type="password" name="password" placeholder="Leave blank to keep current password">

      <button type="submit">Save Changes</button>
      <div id="popupMessage"></div>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $('#settingsForm').submit(function(e) {
      e.preventDefault();
      if (!checkPassword()) {
        return
      }
      $.post('update_settings.php', $(this).serialize(), function(response) {
        let popup = $('#popupMessage');
        popup.removeClass('success error');

        if (response.includes('successfully')) {
          popup.addClass('success').text('✅ ' + response);
        } else {
          popup.addClass('error').text('❌ ' + response);
        }

        popup.fadeIn().delay(3000).fadeOut();
        $('input[name="password"]').val('');
      });
    });

    function checkPassword() {
      pass = document.querySelector('input[name="password"]');
      if (pass.value.trim() == '' || pass.value.replace(/\s+/g, '') == '') {
        alert("Invalid Password will not be changed");
        pass.value = ''
        return false
      }
      if (pass.value.length < 6) {
        alert("Password Must be 6 character long")
        return false;
      }
      return true
    }
  </script>

</body>

</html>