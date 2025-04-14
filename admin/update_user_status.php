<?php
include '../common/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];

    if (isset($_POST['block'])) {
        $status = 0;
        $message = "User has been permanently blocked.";
    } elseif (isset($_POST['temp'])) {
        $status = 2;
        $message = "User has been temporarily blocked.";
    } elseif (isset($_POST['activate'])) {
        $status = 1;
        $message = "User has been activated.";
    } else {
        echo "Invalid action.";
        exit;
    }

    $sql = "UPDATE users SET user_status = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $status, $userId);

    if ($stmt->execute()) {
        echo $message;
    } else {
        echo "Failed to update user status.";
    }

    $stmt->close();
    $conn->close();
}
?>
