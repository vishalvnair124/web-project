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
            background: #ffc8c8;
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

        .buttonView {
            border-radius: 5px;
            height: fit-content;
            padding: 7px 30px;
            border: none;
            background-color: #ccc;
            cursor: pointer;
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

        input:not([type='radio']),
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button[type="submit"] {
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
        <form action="process_update.php" method="POST" onsubmit="return checkSubmit()">
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>New Password(If you want to change it)</label>
            <div style='display:flex;align-items:center;gap:5px;'><input type="password" name="pass" id='passMain'><button onclick='showPass("passMain")' type='button' class='buttonView'>View</button></div>
            <input type="hidden" name='passTemp' value="<?= htmlspecialchars($user['password']) ?>">

            <label>Email</label>
            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

            <label>Phone Number</label>
            <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>

            <label>Gender</label><br>
            <?php $g = $user['user_gender']; ?>
            <div class='Gender-div'>
                <table>
                    <tr>
                        <td>Male</td>
                        <td><input type="radio" name="gender" value="Male" <?= $g == "Male" ? "checked" : "" ?>></td>
                    </tr>
                    <tr>
                        <td>Female</td>
                        <td><input type="radio" name="gender" value="Female" <?= $g == "Female" ? "checked" : "" ?>></td>
                    </tr>
                    <tr>
                        <td>Other</td>
                        <td><input type="radio" name="gender" value="Other" <?= $g == "Other" ? "checked" : "" ?>></td>
                    </tr>
                </table>
            </div>

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
            <input type="date" name="user_dob" id='dob' onchange='calculateAge()' value="<?= $donor['user_dob'] ?? '' ?>" required>
            <label>Availability Status</label>
            <select name="availability_status" id='Avalibility' onchange='chechState()' required>
                <option value="1" <?= ($user['availability_status'] ?? '') == '1' ? 'selected' : '' ?>>Available</option>
                <option value="0" <?= ($user['availability_status'] ?? '') == '0' ? 'selected' : '' ?>>Not Available</option>
            </select>
            <br>
            <h3>Health Details</h3>

            <label>Weight (kg)</label>
            <input type="number" step="0.1" id='weight' name="weight" value="<?= $donor['weight'] ?? '' ?>">

            <label>Height (cm)</label>
            <input type="number" step="0.1" id='height' name="height" value="<?= $donor['height'] ?? '' ?>">

            <label>Blood Pressure</label>
            <input type="text" name="blood_pressure" id='bp' value="<?= $donor['blood_pressure'] ?? '' ?>">

            <label>Pulse Rate (bpm)</label>
            <input type="number" name="pulse_rate" id='pr' value="<?= $donor['pulse_rate'] ?? '' ?>">

            <label>Body Temperature (°C)</label>
            <input type="number" step="0.1" id='bt' name="body_temperature" value="<?= $donor['body_temperature'] ?? '' ?>">

            <label>Hemoglobin Level (g/dL)</label>
            <input type="number" step="0.1" id='hk' name="hemoglobin_level" value="<?= $donor['hemoglobin_level'] ?? '' ?>">

            <label>Cholesterol Level (mg/dL)</label>
            <input type="number" step="0.1" id='cl' name="cholesterol" value="<?= $donor['cholesterol'] ?? '' ?>">

            <label>Chronic Diseases</label>
            <input type="text" name="chronic_diseases" id='cd' value="<?= $donor['chronic_diseases'] ?? '' ?>">

            <label>Medications</label>
            <input type="text" name="medications" id='med' value="<?= $donor['medications'] ?? '' ?>">

            <label>Current Location</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" name="latitude" id="latitude" readonly placeholder="Latitude"
                    value="<?= $user['latitude'] ?? '' ?>">
                <input type="text" name="longitude" id="longitude" readonly placeholder="Longitude"
                    value="<?= $user['longitude'] ?? '' ?>">
                <button type="button" style="margin-top:0px;" onclick="updateLocation()">Update</button>
            </div>




            <label>Alcohol Consumption</label>
            <select name="alcohol_consumption" id='alc'>
                <option value="">Select</option>
                <option value="Yes" <?= ($donor['alcohol_consumption'] ?? '') == 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= ($donor['alcohol_consumption'] ?? '') == 'No' ? 'selected' : '' ?>>No</option>
            </select>

            <label>Tattoos or Piercings</label>
            <select name="tattoos_piercings" id='tattoos'>
                <option value="">Select</option>
                <option value="Yes" <?= ($donor['tattoos_piercings'] ?? '') == 'Yes' ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= ($donor['tattoos_piercings'] ?? '') == 'No' ? 'selected' : '' ?>>No</option>
            </select>

            <label>Pregnancy Status</label>
            <select name="pregnancy_status" id='preg'>
                <option value="">Select</option>
                <option value="Pregnant" <?= ($donor['pregnancy_status'] ?? '') == 'Yes' ? 'selected' : '' ?>>Pregnant</option>
                <option value="Not Pregnant" <?= ($donor['pregnancy_status'] ?? '') == 'No' ? 'selected' : '' ?>>Not Pregnant</option>
                <option value="Not Applicable" <?= ($donor['pregnancy_status'] ?? '') == 'N/A' ? 'selected' : '' ?>>Not Applicable</option>
            </select>
            <button type="submit">Update Profile</button>
        </form>
    </div>


    <script>
        const DonorDetailsIn = ['weight', 'height', 'bp', 'pr', , 'bt', 'hk', 'cl', 'cd', 'med'];
        const DonorDetailsSel = ['alc', 'tattoos', 'preg'];

        function chechState() {
            if (document.getElementById("Avalibility").value == 1) {
                DonorDetailsIn.forEach(ele => {
                    document.getElementById(ele).removeAttribute("readonly")
                    document.getElementById(ele).style.color = 'black'
                })
                DonorDetailsSel.forEach(ele => {
                    document.getElementById(ele).removeAttribute("disabled")
                });
            } else {
                DonorDetailsIn.forEach((ele) => {
                    document.getElementById(ele).setAttribute("readonly", true)
                    document.getElementById(ele).style.color = 'gray'
                })
                DonorDetailsSel.forEach(ele => {
                    document.getElementById(ele).setAttribute("disabled", true)
                });
            }
        }
        chechState()

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

        function showPass(objName) {
            object = document.getElementById(objName);
            if (object.type == "text") {
                object.type = "password";
            } else {
                object.type = "text";
            }
        }

        function checkSubmit() {

            if (!calculateAge()) {
                return false
            }

            if ((document.getElementById("passMain").value).length == 0) {
                return true;
            }
            if (((document.getElementById("passMain").value).trim() == "") && (document.getElementById("passMain").value.replace(/\s+/g, '') == "")) {
                document.getElementById("passMain").value = '';
                alert("Password will not be changed")
                return true;
            }
            if (document.getElementById("passMain").value.length < 6) {
                alert("Password must be 6 character long");
                return false
            }
            return confirm("Are you sure you want to change the password?")
        }

        function calculateAge() {
            const dobInput = document.getElementById('dob').value;

            const dob = new Date(dobInput);
            const today = new Date();

            let age = today.getFullYear() - dob.getFullYear();
            const monthDiff = today.getMonth() - dob.getMonth();
            const dayDiff = today.getDate() - dob.getDate();

            if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                age--;
            }
            if (age < 18) {
                alert("Age must be above 18");
                return false;
            }
            return true;

        }

        // Optional: auto-update on load
        window.onload = updateLocation;
    </script>


</body>

</html>