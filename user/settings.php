<?php
session_start();
include '../common/connection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_sql = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

// Fetch donor health details (if available)
$donor_sql = "SELECT * FROM donor_info WHERE user_id = $user_id";
$donor_result = $conn->query($donor_sql);
$donor = ($donor_result->num_rows > 0) ? $donor_result->fetch_assoc() : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Update Donor Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to bottom right, #e53935, #1e88e5);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #c62828;
        }

        form label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #c62828;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Update Donor Profile</h2>
        <form action="process_update.php" method="POST">
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>Email</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

            <label>Phone Number</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

            <label>Gender</label><br>
            <?php $g = $user['user_gender']; ?>
            <input type="radio" name="gender" value="Male" <?= $g == "Male" ? "checked" : "" ?>> Male
            <input type="radio" name="gender" value="Female" <?= $g == "Female" ? "checked" : "" ?>> Female
            <input type="radio" name="gender" value="Other" <?= $g == "Other" ? "checked" : "" ?>> Other

            <label>Blood Group</label>
            <select name="blood_group" required>
                <?php
                $groups = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                foreach ($groups as $bg) {
                    echo "<option value='$bg'" . ($user['blood_group'] == $bg ? ' selected' : '') . ">$bg</option>";
                }
                ?>
            </select>
            <label>Date of Birth</label>
            <input type="date" name="user_dob" value="<?= $donor['user_dob'] ?? '' ?>" required>
            <label>Availability Status</label>
            <select name="availability_status" required>
                <option value="">Select</option>
                <option value="1" <?= ($donor['availability_status'] ?? '') == '1' ? 'selected' : '' ?>>Available</option>
                <option value="0" <?= ($donor['availability_status'] ?? '') == '0' ? 'selected' : '' ?>>Not Available</option>
            </select>

            <br>
            <h3>Health Details</h3>

            <label>Weight (kg)</label>
            <input type="number" step="0.1" name="weight" value="<?= $donor['weight'] ?? '' ?>">

            <label>Height (cm)</label>
            <input type="number" step="0.1" name="height" value="<?= $donor['height'] ?? '' ?>">

            <label>Blood Pressure</label>
            <input type="text" name="blood_pressure" value="<?= $donor['blood_pressure'] ?? '' ?>">

            <label>Pulse Rate (bpm)</label>
            <input type="number" name="pulse_rate" value="<?= $donor['pulse_rate'] ?? '' ?>">

            <label>Body Temperature (°C)</label>
            <input type="number" step="0.1" name="body_temperature" value="<?= $donor['body_temperature'] ?? '' ?>">

            <label>Hemoglobin Level (g/dL)</label>
            <input type="number" step="0.1" name="hemoglobin_level" value="<?= $donor['hemoglobin_level'] ?? '' ?>">

            <label>Cholesterol Level (mg/dL)</label>
            <input type="number" step="0.1" name="cholesterol" value="<?= $donor['cholesterol'] ?? '' ?>">

            <label>Chronic Diseases</label>
            <input type="text" name="chronic_diseases" value="<?= $donor['chronic_diseases'] ?? '' ?>">

            <label>Medications</label>
            <input type="text" name="medications" value="<?= $donor['medications'] ?? '' ?>">

            <label>Current Location</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" name="latitude" id="latitude" readonly placeholder="Latitude"
                    value="<?= $user['latitude'] ?? '' ?>">
                <input type="text" name="longitude" id="longitude" readonly placeholder="Longitude"
                    value="<?= $user['longitude'] ?? '' ?>">
                <button type="button" style="margin-top:0px;" onclick="updateLocation()">Update</button>
            </div>




            <label>Alcohol Consumption</label>
            <select name="alcohol_consumption">
                <option value="">Select</option>
                <option value="Yes" <?= ($donor['alcohol_consumption'] ?? '') == 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= ($donor['alcohol_consumption'] ?? '') == 'No' ? 'selected' : '' ?>>No</option>
            </select>

            <label>Tattoos or Piercings</label>
            <select name="tattoos_piercings">
                <option value="">Select</option>
                <option value="Yes" <?= ($donor['tattoos_piercings'] ?? '') == 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= ($donor['tattoos_piercings'] ?? '') == 'No' ? 'selected' : '' ?>>No</option>
            </select>

            <label>Pregnancy Status</label>
            <select name="pregnancy_status">
                <option value="">Select</option>
                <option value="Pregnant" <?= ($donor['pregnancy_status'] ?? '') == 'Pregnant' ? 'selected' : '' ?>>Pregnant</option>
                <option value="Not Pregnant" <?= ($donor['pregnancy_status'] ?? '') == 'Not Pregnant' ? 'selected' : '' ?>>Not Pregnant</option>
                <option value="Not Applicable" <?= ($donor['pregnancy_status'] ?? '') == 'Not Applicable' ? 'selected' : '' ?>>Not Applicable</option>
            </select>
            <button type="submit">Update Profile</button>
        </form>
    </div>


    <script>
        function updateLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;

                    // alert("Location updated successfully!");
                }, function(error) {
                    console.error("Location error: ", error.message);
                    alert("Location access is needed to update your profile correctly.");
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Optional: auto-update on load
        window.onload = updateLocation;
    </script>


</body>

</html>