<?php
// Include the database connection file
include '../common/connection.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = intval($_POST['request_id']); // Sanitize and store the request ID

    // Check if the update request button was clicked
    if (isset($_POST['update_request'])) {
        $latitude = $_POST['latitude']; // Latitude of the request location
        $longitude = $_POST['longitude']; // Longitude of the request location
        $place = $_POST['place']; // Place name of the request location
        $blood_group = $_POST['blood_group']; // Blood group required
        $request_units = $_POST['request_units']; // Number of units required
        $when_need_blood = $_POST['when_need_blood']; // Date and time when blood is needed
        $hospital_name = $_POST['hospital_name']; // Name of the hospital
        $doctor_name = $_POST['doctor_name']; // Name of the doctor
        $additional_notes = $_POST['additional_notes']; // Additional notes for the request

        // SQL query to update the blood request details
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

        // Execute the query and check if the update was successful
        if ($conn->query($sql) === TRUE) {
            echo "<script> window.location.href='../user/?page=requests.php';</script>"; // Redirect to the requests page
        } else {
            echo "Error updating request: " . $conn->error; // Display an error message if the update fails
        }
    } elseif (isset($_POST['re_request'])) { // Check if the re-request button was clicked
        $check_sql = "SELECT request_level FROM blood_requests WHERE request_id = $request_id"; // Query to check the current request level
        $result = $conn->query($check_sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_level = $row['request_level']; // Get the current request level

            if ($current_level >= 2) { // Check if the maximum request level is reached
                echo "<script>window.location.href='../user/?page=requests.php&msg=maxlevel';</script>"; // Redirect with a message
            } else {
                // Proceed with updating the request status and level
                $sql = "UPDATE blood_requests SET 
                    request_status = 1, 
                    request_level = request_level + 1 
                WHERE request_id = $request_id";

                // Execute the query and redirect to create a notification
                if ($conn->query($sql) === TRUE) {
                    echo "<script>
                    window.location.href='../search/create_notification.php?request_id=" . $request_id . "';
                  </script>";
                }
            }
        } else {
            // Display an error message if something goes wrong
            echo "<script>alert('Something is wrong!'); window.location.href='../user/?page=requests.php';</script>";
        }
    } elseif (isset($_POST['delete_request'])) { // Check if the delete request button was clicked
        // SQL query to mark the request as deleted
        $sql = "UPDATE blood_requests SET request_status = 6 WHERE request_id = $request_id";
        if ($conn->query($sql) === TRUE) {
            echo "<script>  window.location.href='../user/?page=requests.php';</script>"; // Redirect to the requests page
        }
    }
}

// Close the database connection
$conn->close();
