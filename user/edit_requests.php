<?php
// Include database connection
include '../common/connection.php';

// Ensure request_id is set in the URL
if (!isset($_GET['request_id'])) {
    echo "<script>alert('Request ID is missing!'); window.location.href='?page=requests.php';</script>";
    exit();
}

// Get the request ID from the URL
$request_id = intval($_GET['request_id']);

// Fetch the blood request details from the database
$query = "SELECT * FROM blood_requests WHERE request_id = $request_id";
$result = $conn->query($query);

// If no request is found, display an error message and redirect
if ($result->num_rows == 0) {
    echo "<script>alert('Request not found!'); window.location.href='?page=requests.php';</script>";
    exit();
}

// Fetch the request details as an associative array
$row = $result->fetch_assoc();

// Close the database connection
$conn->close();
?>

<link rel="stylesheet" href="styles/styles.css"> <!-- Link to external CSS -->

<div class="form-container">
    <h2>Edit Blood Request</h2>
    <a href="http://localhost/dropforlife/user/?page=requests.php" class="back-btn">Back</a> <!-- Back button -->

    <!-- Form to edit the blood request -->
    <form method="POST" action="update_requests.php">
        <!-- Hidden input to store the request ID -->
        <input type="hidden" name="request_id" value="<?= $request_id ?>">

        <!-- Display blood group (read-only) -->
        <label>Blood Group</label>
        <input type="text" name="blood_group" value="<?= $row['blood_group'] ?>" required readonly>

        <!-- Display units required (read-only) -->
        <label>Units Required</label>
        <input type="number" name="request_units" value="<?= $row['request_units'] ?>" required readonly>

        <!-- Input for when blood is needed -->
        <label>When Needed</label>
        <input type="datetime-local" name="when_need_blood" value="<?= date('Y-m-d\TH:i', strtotime($row['when_need_blood'])) ?>" required>

        <!-- Input for hospital name -->
        <label>Hospital Name</label>
        <input type="text" name="hospital_name" value="<?= $row['hospital_name'] ?>" required>

        <!-- Input for doctor name -->
        <label>Doctor Name</label>
        <input type="text" name="doctor_name" value="<?= $row['doctor_name'] ?>" required>

        <!-- Input for additional notes -->
        <label>Additional Notes</label>
        <textarea name="additional_notes"><?= $row['additional_notes'] ?></textarea>

        <!-- Display latitude (read-only) -->
        <label>Latitude</label>
        <input type="text" name="latitude" id="latitude" value="<?= $row['latitude'] ?>" readonly>

        <!-- Display longitude (read-only) -->
        <label>Longitude</label>
        <input type="text" name="longitude" id="longitude" value="<?= $row['longitude'] ?>" readonly>

        <!-- Display place (read-only) -->
        <label>Place</label>
        <input type="text" name="place" id="place" value="<?= $row['place'] ?>" readonly>

        <?php
        // Check the current status of the request
        $request_status = $row['request_status'];
        if ($request_status == 1 || $request_status == 3): ?>
            <!-- Buttons for updating, re-requesting, or closing the request -->
            <button type="submit" name="update_request" class="btn update-btn">Update</button>
            <button type="submit" name="re_request" class="btn re-request-btn">Re-request</button>
            <button type="submit" name="delete_request" class="btn delete-btn">Close Request</button>
        <?php else: ?>
            <!-- Display the status of the request -->
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
    // Function to fetch the user's current location
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async function(position) {
                    const latitude = position.coords.latitude; // Get latitude
                    const longitude = position.coords.longitude; // Get longitude

                    // Update latitude and longitude fields
                    document.getElementById("latitude").value = latitude;
                    document.getElementById("longitude").value = longitude;

                    // If location is not available, display an error
                    if (!latitude || !longitude) {
                        alert("Could not get location. Please try again.");
                        return;
                    }

                    // Fetch location details using the backend
                    const response = await fetch(`get_location.php?lat=${latitude}&lon=${longitude}`);
                    const data = await response.json();

                    // Update the place field with the fetched location
                    if (data.place) {
                        document.getElementById("place").value = data.place;
                    } else {
                        document.getElementById("place").value = "Unknown Location";
                    }
                },
                function(error) {
                    // Handle geolocation errors
                    alert("Error fetching location: " + error.message);
                });
        } else {
            // Handle unsupported browsers
            alert("Geolocation is not supported by this browser.");
        }
    }
</script>

<style>
    /* Styling for the status box */
    .status-box {
        padding: 15px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 20px;
        text-align: center;
    }

    /* Styling for completed requests */
    .completed {
        background-color: #C8E6C9;
        color: #256029;
    }

    /* Styling for blocked requests */
    .blocked {
        background-color: #FFECB3;
        color: #8D6E63;
    }

    /* Styling for deleted requests */
    .deleted {
        background-color: #FFCDD2;
        color: #C62828;
    }

    /* Styling for the form container */
    .form-container {
        max-width: 700px;
        margin: auto;
        background: #FFFFFF;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        margin-top: 50px;
    }

    /* Styling for the form header */
    .form-container h2 {
        text-align: center;
        color: #D32F2F;
        font-size: 24px;
        margin-bottom: 20px;
    }

    /* Styling for form labels */
    .form-container label {
        display: block;
        font-weight: bold;
        margin: 10px 0 5px;
        color: #444;
    }

    /* Styling for form inputs, selects, and textareas */
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

    /* Focus styling for form inputs, selects, and textareas */
    .form-container input:focus,
    .form-container select:focus,
    .form-container textarea:focus {
        border-color: #D32F2F;
        background: #FFFFFF;
        outline: none;
    }

    /* Styling for buttons */
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

    /* Styling for the update button */
    .update-btn {
        background: #1976D2;
        color: white;
    }

    /* Hover effect for the update button */
    .update-btn:hover {
        background: #0D47A1;
    }
</style>