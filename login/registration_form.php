<!-- registration_form.php -->
<?php
session_start();
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
  header("Location: otpscreen.php?error=unauthorized");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Donor Registration - Drop4Life 🩸</title>
  <style>
    /* your existing CSS remains unchanged */
  </style>
  <script>
    function toggleDonorDetails(value) {
      document.getElementById('donor-details').style.display = (value === 'Yes') ? 'block' : 'none';
    }
  </script>
</head>

<body>
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

    h2,
    h3 {
      text-align: center;
      color: #c62828;
    }

    form label {
      display: block;
      margin-top: 15px;
      font-weight: 600;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="date"],
    input[type="number"],
    input[type="password"],
    select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
    }

    .buttonView {
      border-radius: 5px;
      height: fit-content;
      padding: 7px 30px;
      border: none;
      background-color: #ccc;
      cursor: pointer;
    }

    input[type="radio"] {
      margin: 0 5px 0 15px;
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
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #a91e1e;
    }

    hr {
      margin: 30px 0;
    }

    #donor-details {
      display: none;
    }
  </style>
  <div class="container">
    <h2>Registration Form 🩸</h2>
    <form action="submit_registration.php" method="POST" onsubmit="return checkSubmit()">
      <label>Full Name</label>
      <input type="text" name="fullname" required>

      <label>New Password</label>
      <div style='display:flex;align-items:center;gap:5px;'><input type="password" name="pass" id='passMain' required><button onclick='showPass("passMain")' type='button' class='buttonView'>View</button></div>
      <label>Confirm New Password</label>
      <div style='display:flex;align-items:center;gap:5px;'><input type="password" id='passCo' required><button onclick='showPass("passCo")' type='button' class='buttonView'>View</button></div>

      <label>Email Address</label>
      <input type="email" name="email" id='EnEmail' required value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" readonly>

      <label>Phone Number</label>
      <input type="tel" name="phone" id='phoneNumber' required>

      <label>Birth Date</label>
      <input type="date" name="birthdate" id='dob' onchange='calculateAge()' required>

      <label>Gender</label><br>
      <input type="radio" name="gender" value="Male" required> Male
      <input type="radio" name="gender" value="Female"> Female
      <input type="radio" name="gender" value="Prefer not to say"> Prefer not to say

      <label>Latitude</label>
      <input type="text" id="latitude" name="latitude" required readonly placeholder="Fetching latitude...">

      <label>Longitude</label>
      <input type="text" id="longitude" name="longitude" required readonly placeholder="Fetching longitude...">

      <label>Blood Group</label>
      <select name="blood_group" required>
        <option value="">Select Blood Group</option>
        <option value="A+">A+</option>
        <option value="A-">A-</option>
        <option value="B+">B+</option>
        <option value="B-">B-</option>
        <option value="O+">O+</option>
        <option value="O-">O-</option>
        <option value="AB+">AB+</option>
        <option value="AB-">AB-</option>
      </select>


      <label>Are you interested in donating blood?</label>
      <select name="interested" onchange="toggleDonorDetails(this.value)" required>
        <option value="">Select</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>

      <div id="donor-details" style="display:none;">
        <hr>
        <h3>Health Details</h3>

        <label>Weight (kg)</label>
        <input type="number" name="weight" step="0.1">

        <label>Height (cm)</label>
        <input type="number" name="height" step="0.1">

        <label>Pulse Rate (bpm)</label>
        <input type="number" name="pulse_rate">

        <label>Body Temperature (°C)</label>
        <input type="number" name="body_temperature" step="0.1">

        <label>Blood Pressure</label>
        <input type="text" name="blood_pressure">

        <label>Hemoglobin Level (g/dL)</label>
        <input type="number" name="hemoglobin_level" step="0.1">

        <label>Cholesterol Level (mg/dL)</label>
        <input type="number" name="cholesterol" step="0.1">
        <label>Do you have any chronic diseases?</label>
        <input type="text" name="chronic_diseases">

        <label>Are you currently taking any medications?</label>
        <input type="text" name="medications">

        <label>Do you consume alcohol?</label>
        <select name="alcohol_consumption">
          <option value="">Select</option>
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>

        <label>Do you have any tattoos or piercings?</label>
        <select name="tattoos_piercings">
          <option value="">Select</option>
          <option value="Yes">Yes</option>
          <option value="No">No</option>
        </select>

        <label>Pregnancy Status (if applicable)</label>
        <select name="pregnancy_status">
          <option value="">Select</option>
          <option value="Pregnant">Pregnant</option>
          <option value="Not Pregnant">Not Pregnant</option>
          <option value="Not Applicable">Not Applicable</option>
        </select>





      </div>

      <button type="submit">Submit 🩸</button>
    </form>
  </div>
  <script>
    window.onload = function() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
          document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
        }, function(error) {
          alert("Location access denied or unavailable. Please enable location services.");
        });
      } else {
        alert("Geolocation is not supported by this browser.");
      }
    };

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
      if (document.getElementById("phoneNumber").value.length != 10) {
        alert("Enter a valid phone number")
        return false
      }
      // if(!isValidEmail(document.getElementById("EnEmail").value)){
      //   alert("Enter a valid email")
      //   return false
      // }
      if (document.getElementById("passMain").value.length < 6) {
        alert("Password must be 6 character long")
        return false;
      }
      if ((document.getElementById("passCo").value != document.getElementById("passMain").value)) {
        alert("Password Not Matching");
        return false;
      }
      return true
    }


    function isValidEmail(email) {
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return regex.test(email);
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
  </script>

</body>

</html>