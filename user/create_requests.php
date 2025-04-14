<?php
// Start session and include necessary files
session_start();
include '../common/connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}
?>
<link rel="stylesheet" href="leaflet.css" />
<link rel="stylesheet" href="styles/styles.css">

<style>
    #map {
        height: 300px;
        /* Ensure the map is visible */
        margin: 10px 0;
        border-radius: 10px;
    }
</style>

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


        <div id="map"></div>
        <p>Selected Location: <span id="location"></span></p>
        <style>
            #location,
            p {
                display: none;
                visibility: hidden;
                opacity: 0;
            }
        </style>
        <script src="leaflet.js"></script>



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
    var map = L.map('map').setView([20.5937, 78.9629], 5); // Center on India

    // Add OpenStreetMap layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker;

    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;

        // Update Marker Position
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }

        // Update Input Fields
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
        let locationSpan = document.getElementById("location");
        if (locationSpan) {
            locationSpan.innerText = `Lat: ${lat}, Lng: ${lng}`;
        }
        latValue = lat.toFixed(6);
        lngValue = lng.toFixed(6);

        // Fetch Place Name
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latValue}&lon=${lngValue}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById("place").value = data.display_name || "Unknown Location";
            })
            .catch(() => document.getElementById("place").value = "Unknown Location");
    });

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Update Marker & Move Map
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng]).addTo(map);
                    }
                    map.setView([lat, lng], 15); // Zoom to current location

                    // Update Input Fields
                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;
                    document.getElementById("location").innerText = `Lat: ${lat}, Lng: ${lng}`;


                    // Fetch Place Name
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                    const data = await response.json();

                    if (data.display_name) {
                        document.getElementById("place").value = data.display_name;
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
    document.querySelector("form").addEventListener("submit", function(e) {
        const bloodGroup = document.querySelector('[name="blood_group"]').value;
        const units = parseInt(document.querySelector('[name="request_units"]').value);
        const whenNeeded = new Date(document.querySelector('[name="when_need_blood"]').value);
        const hospital = document.querySelector('[name="hospital_name"]').value.trim();
        const doctor = document.querySelector('[name="doctor_name"]').value.trim();
        const latitude = document.querySelector('[name="latitude"]').value.trim();
        const longitude = document.querySelector('[name="longitude"]').value.trim();
        const place = document.querySelector('[name="place"]').value.trim();

        let errors = [];

        if (!bloodGroup) errors.push("Blood group is required.");

        if (isNaN(units) || units < 1 || units > 10) {
            errors.push("Units must be a number between 1 and 10.");
        }

        const now = new Date();
        if (!whenNeeded || whenNeeded <= now) {
            errors.push("Please select a valid future date and time for 'When Needed'.");
        }

        if (!hospital) errors.push("Hospital name is required.");
        if (!doctor.match(/^[a-zA-Z\s]+$/)) {
            errors.push("Doctor name should contain only letters and spaces.");
        }

        if (!latitude || !longitude || !place) {
            errors.push("Location must be selected using the map or 'Get Current Location'.");
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    });
</script>