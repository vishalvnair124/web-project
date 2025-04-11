<?php
include '../common/connection.php'; // ✅ use the correct connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // Decide status based on which button was clicked
    if (isset($_POST['block'])) {
        $status = 0;
    } elseif (isset($_POST['temp'])) {
        $status = 2;
    } elseif (isset($_POST['activate'])) {
        $status = 1;
    } else {
        header("Location: users.php");
        exit();
    }

    // Update status in DB
    $stmt = $conn->prepare("UPDATE users SET user_status = ? WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $status, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: users.php");
    exit();
} else {
    header("Location: users.php");
    exit();
}
?>