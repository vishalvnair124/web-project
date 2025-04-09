<?php
// Include database connection
include '../common/connection.php';

// Ensure request_id is set
if (!isset($_GET['request_id'])) {
    echo "<script>alert('Request ID is missing!'); window.location.href='?page=requests.php';</script>";
    exit();
}

$request_id = intval($_GET['request_id']);
$query = "SELECT * FROM blood_requests WHERE request_id = $request_id";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "<script>alert('Request not found!'); window.location.href='?page=requests.php';</script>";
    exit();
}

$row = $result->fetch_assoc();
$conn->close();
?>

<link rel="stylesheet" href="styles/styles.css"> <!-- Link to external CSS -->

<div class="form-container">
    <h2>Edit Blood Request</h2>
    <a href="http://localhost/dropforlife/user/?page=requests.php" class="back-btn">Back</a>

    <form method="POST" action="update_requests.php">
        <input type="hidden" name="request_id" value="<?= $request_id ?>">

        <label>Blood Group</label>
        <input type="text" name="blood_group" value="<?= $row['blood_group'] ?>" required>

        <label>Units Required</label>
        <input type="number" name="request_units" value="<?= $row['request_units'] ?>" required>

        <label>When Needed</label>
        <input type="datetime-local" name="when_need_blood" value="<?= date('Y-m-d\TH:i', strtotime($row['when_need_blood'])) ?>" required>

        <label>Hospital Name</label>
        <input type="text" name="hospital_name" value="<?= $row['hospital_name'] ?>" required>

        <label>Doctor Name</label>
        <input type="text" name="doctor_name" value="<?= $row['doctor_name'] ?>" required>

        <label>Additional Notes</label>
        <textarea name="additional_notes"><?= $row['additional_notes'] ?></textarea>

        <label>Latitude</label>
        <input type="text" name="latitude" id="latitude" value="<?= $row['latitude'] ?>" readonly>

        <label>Longitude</label>
        <input type="text" name="longitude" id="longitude" value="<?= $row['longitude'] ?>" readonly>

        <label>Place</label>
        <input type="text" name="place" id="place" value="<?= $row['place'] ?>" readonly>

        <?php

        $request_status = $row['request_status'];
        if ($request_status == 1 || $request_status == 3): ?>
            <!-- <button type="button" class="btn fetch-btn" onclick="getLocation()">Fetch Current Location</button> -->
            <button type="submit" name="update_request" class="btn update-btn">Update</button>
            <button type="submit" name="re_request" class="btn re-request-btn">Re-request</button>
            <button type="submit" name="delete_request" class="btn delete-btn">Close Request</button>
        <?php else: ?>
            <div class="status-box">
                <?php
                if ($request_status == 4) {
                    echo "✅ Request has been successfully completed.";
                } elseif ($request_status == 5) {
                    echo "⛔ Request has been temporarily blocked by admin.";
                } elseif ($request_status == 6) {
                    echo "❌ This request has been deleted by you.";
                }
                ?>
            </div>
        <?php endif; ?>

    </form>
</div>

<script>
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    document.getElementById("latitude").value = latitude;
                    document.getElementById("longitude").value = longitude;

                    if (!latitude || !longitude) {
                        alert("Could not get location. Please try again.");
                        return;
                    }

                    // Fetch location details using the backend
                    const response = await fetch(`get_location.php?lat=${latitude}&lon=${longitude}`);
                    const data = await response.json();

                    if (data.place) {
                        document.getElementById("place").value = data.place;
                    } else {
                        document.getElementById("place").value = "Unknown Location";
                    }
                },
                function(error) {
                    alert("Error fetching location: " + error.message);
                });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }
</script>


<script>
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    document.getElementById("latitude").value = latitude;
                    document.getElementById("longitude").value = longitude;

                    if (!latitude || !longitude) {
                        alert("Could not get location. Please try again.");
                        return;
                    }

                    const response = await fetch(`get_location.php?lat=${latitude}&lon=${longitude}`);
                    const data = await response.json();

                    if (data.place) {
                        document.getElementById("place").value = data.place;
                    } else {
                        document.getElementById("place").value = "Unknown Location";
                    }
                },
                function(error) {
                    alert("Error fetching location: " + error.message);
                });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }
</script>


<style>
    .status-box {
        padding: 15px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 20px;
        text-align: center;
    }

    .completed {
        background-color: #C8E6C9;
        color: #256029;
    }

    .blocked {
        background-color: #FFECB3;
        color: #8D6E63;
    }

    .deleted {
        background-color: #FFCDD2;
        color: #C62828;
    }


    .form-container {
        max-width: 700px;
        margin: auto;
        background: #FFFFFF;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        margin-top: 50px;
    }

    .form-container h2 {
        text-align: center;
        color: #D32F2F;
        font-size: 24px;
        margin-bottom: 20px;
    }

    .form-container label {
        display: block;
        font-weight: bold;
        margin: 10px 0 5px;
        color: #444;
    }

    .form-container input,
    .form-container select,
    .form-container textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #DDDDDD;
        border-radius: 8px;
        font-size: 16px;
        background: #FAFAFA;
        transition: 0.3s ease-in-out;
    }

    .form-container input:focus,
    .form-container select:focus,
    .form-container textarea:focus {
        border-color: #D32F2F;
        background: #FFFFFF;
        outline: none;
    }

    .btn {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        transition: 0.3s ease-in-out;
        margin-top: 10px;
    }

    .update-btn {
        background: #1976D2;
        color: white;
    }

    .update-btn:hover {
        background: #0D47A1;
    }
</style>