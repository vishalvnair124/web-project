<?php
// Start session and include necessary files
session_start();
include '../common/connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}
?>

<link rel="stylesheet" href="styles/styles.css">

<div class="form-container">
    <div class="header">
        <h2>Create Blood Request</h2>
        <a href="?page=requests" class="back-btn">Back</a>
    </div>

    <form action="process_request.php" method="POST">
        <label>Blood Group:</label>
        <select name="blood_group" required>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
        </select>

        <label>Units Required:</label>
        <input type="number" name="request_units" min="1" required>

        <label>When Needed:</label>
        <input type="datetime-local" name="when_need_blood" required>

        <label>Hospital Name:</label>
        <input type="text" name="hospital_name" required>

        <label>Doctor Name:</label>
        <input type="text" name="doctor_name" required>

        <label>Additional Notes:</label>
        <textarea name="additional_notes"></textarea>

        <label>Latitude:</label>
        <input type="text" id="latitude" name="latitude" readonly required>

        <label>Longitude:</label>
        <input type="text" id="longitude" name="longitude" readonly required>

        <label>Place:</label>
        <input type="text" id="place" name="place" readonly required>

        <button type="button" class="location-btn" onclick="getLocation()">Get Current Location</button>
        <button type="submit" class="submit-btn">Submit Request</button>
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