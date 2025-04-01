<?php
include '../common/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = intval($_POST['request_id']);

    if (isset($_POST['update_request'])) {
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $place = $_POST['place'];
        $blood_group = $_POST['blood_group'];
        $request_units = $_POST['request_units'];
        $when_need_blood = $_POST['when_need_blood'];
        $hospital_name = $_POST['hospital_name'];
        $doctor_name = $_POST['doctor_name'];
        $additional_notes = $_POST['additional_notes'];

        $sql = "UPDATE blood_requests SET 
                blood_group='$blood_group', 
                request_units='$request_units', 
                when_need_blood='$when_need_blood', 
                hospital_name='$hospital_name', 
                doctor_name='$doctor_name', 
                additional_notes='$additional_notes',
                latitude='$latitude', 
                longitude='$longitude', 
                place='$place' 
                WHERE request_id = $request_id";

        if ($conn->query($sql) === TRUE) {
            echo "<script> window.location.href='../user/?page=requests.php';</script>";
        } else {
            echo "Error updating request: " . $conn->error;
        }
    } elseif (isset($_POST['re_request'])) {
        $sql = "UPDATE blood_requests SET 
                request_status = 1, 
                request_level = request_level + 1 
                WHERE request_id = $request_id";

        if ($conn->query($sql) === TRUE) {
            echo "<script>  window.location.href='../user/?page=requests.php';</script>";
        }
    } elseif (isset($_POST['delete_request'])) {
        $sql = "UPDATE blood_requests SET request_status = 6 WHERE request_id = $request_id";
        if ($conn->query($sql) === TRUE) {
            echo "<script>  window.location.href='../user/?page=requests.php';</script>";
        }
    }
}
$conn->close();
