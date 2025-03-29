<?php
session_start();
$logged_in_user_id = $_SESSION['user_id'];

echo $logged_in_user_id;
include '../common/connection.php';



$sql = "SELECT * FROM users WHERE user_id = $logged_in_user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);  // This will properly display the array contents
    }
} else {
    echo "0 results";
}
$conn->close();
