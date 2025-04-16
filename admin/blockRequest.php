<?php
include '../common/connection.php';
include '../common/session_check_admin.php';

$ReqID=$_POST['requestID'];
$Status=$_POST['Status'];
$query = "UPDATE blood_requests SET request_status = ? WHERE request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $Status, $ReqID);
$stmt->execute();
header("location:index.php?page=requests.php");
?>