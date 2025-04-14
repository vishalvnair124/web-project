<?php
// Start session and include necessary files
session_start();
include '../common/connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in."); // Terminate if user is not logged in
}
?>
<link rel="stylesheet" href="leaflet.css" />
<link rel="stylesheet" href="styles/styles.css">

<style>
    /* Styling for the map container */
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
        <a href="?page=requests" class="back-btn">Back</a> <!-- Back button -->
    </div>

    <!-- Form to create a blood request -->
    <form action="process_request.php" method="POST">
        <!-- Blood group selection -->
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

        <!-- Input for units required -->
        <label>Units Required:</label>
        <input type="number" name="request_units" min="1" required>

        <!-- Input for when blood is needed -->
        <label>When Needed:</label>
        <input type="datetime-local" name="when_need_blood" required>

        <!-- Input for hospital name -->
        <label>Hospital Name:</label>
        <input type="text" name="hospital_name" required>

        <!-- Input for doctor name -->
        <label>Doctor Name:</label>
        <input type="text" name="doctor_name" required>

        <!-- Input for additional notes -->
        <label>Additional Notes:</label>
        <textarea name="additional_notes"></textarea>

        <!-- Map for selecting location -->
        <div id="map"></div>
        <p>Selected Location: <span id="location"></span></p>
        <style>
            /* Hide location text initially */
            #location,
            p {
                display: none;
                visibility: hidden;
                opacity: 0;
            }
        </style>
        <script src="leaflet.js"></script>

        <!-- Latitude, Longitude, and Place inputs -->
        <label>Latitude:</label>
        <input type="text" id="latitude" name="latitude" readonly required>

        <label>Longitude:</label>
        <input type="text" id="longitude" name="longitude" readonly required>

        <label>Place:</label>
        <input type="text" id="place" name="place" readonly required>

        <!-- Buttons for location and form submission -->
        <button type="button" class="location-btn" onclick="getLocation()">Get Current Location</button>
        <button type="submit" class="submit-btn">Submit Request</button>
    </form>
</div>

<script>
    // Initialize the map and set its view to a default location (India)
    var map = L.map('map').setView([20.5937, 78.9629], 5);

    // Add OpenStreetMap layer to the map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker; // Variable to store the map marker

    // Event listener for map clicks to update marker and location details
    map.on('click', function(e) {
        var lat = e.latlng.lat; // Latitude of clicked location
        var lng = e.latlng.lng; // Longitude of clicked location

        // Update Marker Position
        if (marker) {
            marker.setLatLng([lat, lng]); // Move existing marker
        } else {
            marker = L.marker([lat, lng]).addTo(map); // Add new marker
        }

        // Update Input Fields
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
        let locationSpan = document.getElementById("location");
        if (locationSpan) {
            locationSpan.innerText = `Lat: ${lat}, Lng: ${lng}`; // Display coordinates
        }
        latValue = lat.toFixed(6); // Round latitude to 6 decimal places
        lngValue = lng.toFixed(6); // Round longitude to 6 decimal places

        // Fetch Place Name using reverse geocoding
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latValue}&lon=${lngValue}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById("place").value = data.display_name || "Unknown Location"; // Update place name
            })
            .catch(() => document.getElementById("place").value = "Unknown Location"); // Handle errors
    });

    // Function to get the user's current location
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(async function(position) {
                    const lat = position.coords.latitude; // Latitude of current location
                    const lng = position.coords.longitude; // Longitude of current location

                    // Update Marker & Move Map
                    if (marker) {
                        marker.setLatLng([lat, lng]); // Move existing marker
                    } else {
                        marker = L.marker([lat, lng]).addTo(map); // Add new marker
                    }
                    map.setView([lat, lng], 15); // Zoom to current location

                    // Update Input Fields
                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;
                    document.getElementById("location").innerText = `Lat: ${lat}, Lng: ${lng}`; // Display coordinates

                    // Fetch Place Name using reverse geocoding
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
                    const data = await response.json();

                    if (data.display_name) {
                        document.getElementById("place").value = data.display_name; // Update place name
                    } else {
                        document.getElementById("place").value = "Unknown Location"; // Handle unknown location
                    }
                },
                function(error) {
                    alert("Error fetching location: " + error.message); // Handle geolocation errors
                });
        } else {
            alert("Geolocation is not supported by this browser."); // Handle unsupported browsers
        }
    }
</script>
<script>
    // Form validation before submission
    document.querySelector("form").addEventListener("submit", function(e) {
        const bloodGroup = document.querySelector('[name="blood_group"]').value; // Selected blood group
        const units = parseInt(document.querySelector('[name="request_units"]').value); // Units required
        const whenNeeded = new Date(document.querySelector('[name="when_need_blood"]').value); // Date and time needed
        const hospital = document.querySelector('[name="hospital_name"]').value.trim(); // Hospital name
        const doctor = document.querySelector('[name="doctor_name"]').value.trim(); // Doctor name
        const latitude = document.querySelector('[name="latitude"]').value.trim(); // Latitude
        const longitude = document.querySelector('[name="longitude"]').value.trim(); // Longitude
        const place = document.querySelector('[name="place"]').value.trim(); // Place name

        let errors = []; // Array to store validation errors

        // Validate blood group
        if (!bloodGroup) errors.push("Blood group is required.");

        // Validate units required
        if (isNaN(units) || units < 1 || units > 10) {
            errors.push("Units must be a number between 1 and 10.");
        }

        // Validate date and time needed
        const now = new Date();
        if (!whenNeeded || whenNeeded <= now) {
            errors.push("Please select a valid future date and time for 'When Needed'.");
        }

        // Validate hospital name
        if (!hospital) errors.push("Hospital name is required.");

        // Validate doctor name
        if (!doctor.match(/^[a-zA-Z\s]+$/)) {
            errors.push("Doctor name should contain only letters and spaces.");
        }

        // Validate location details
        if (!latitude || !longitude || !place) {
            errors.push("Location must be selected using the map or 'Get Current Location'.");
        }

        // If there are errors, prevent form submission and display them
        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    });
</script>