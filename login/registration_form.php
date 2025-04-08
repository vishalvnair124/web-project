<?php
session_start();
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
  // Redirect to OTP page if not verified
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
    body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(to bottom right, #e53935, #1e88e5); /* red to blue gradient */
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
    h2, h3 {
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
    select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
    }
    input[type="radio"] {
      margin: 0 5px 0 15px;
    }
    button[type="submit"], button[type="button"] {
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
    button[type="submit"]:hover, button[type="button"]:hover {
      background-color: #a91e1e;
    }
    hr {
      margin: 30px 0;
    }
    #donor-details {
      display: none;
    }
  </style>
  <script>
    function toggleDonorDetails(value) {
      const donorSection = document.getElementById('donor-details');
      if (value === 'Yes') {
        donorSection.style.display = 'block';
      } else {
        donorSection.style.display = 'none';
      }
    }
  </script>
</head>
<body>
  <div class="container">
    <h2> Registration Form 🩸</h2>
    <form action="submit_registration.php" method="GET">
      <label>Full Name</label>
      <input type="text" name="fullname" required placeholder="Enter full name">

      <label>Email Address</label>
      <input type="email" name="email" required 
       value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>" 
       readonly>

      <label>Phone Number</label>
      <input type="tel" name="phone" required placeholder="Enter phone number">

      <label>Birth Date</label>
      <input type="date" name="birthdate" required>

      <label>Gender</label><br>
      <input type="radio" name="gender" value="Male" required> Male
      <input type="radio" name="gender" value="Female"> Female
      <input type="radio" name="gender" value="Prefer not to say"> Prefer not to say

      <label>Address Line 1</label>
      <input type="text" name="address1" required>

      <label>Address Line 2</label>
      <input type="text" name="address2">

      <label>District</label>
      <input type="text" name="district" required>

      <label>State</label>
      <input type="text" name="state" required>

      <label>Postal Code</label>
      <input type="text" name="postal" required>

      <label>Are you interested in donating blood?</label>
      <select name="interested" onchange="toggleDonorDetails(this.value)" required>
        <option value="">Select</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>

      <div id="donor-details">
        <hr>
        <h3>Health Details</h3>

        <label>Weight (kg)</label>
        <input type="number" name="weight" step="0.1">

        <label>Blood Pressure</label>
        <input type="text" name="blood_pressure" placeholder="e.g., 120/80">

        <label>Hemoglobin Level (g/dL)</label>
        <input type="number" name="hemoglobin_level" step="0.1">

        <label>Blood Group</label>
        <select name="blood_group">
          <option value="A+">A+</option>
          <option value="A-">A-</option>
          <option value="B+">B+</option>
          <option value="B-">B-</option>
          <option value="O+">O+</option>
          <option value="O-">O-</option>
          <option value="AB+">AB+</option>
          <option value="AB-">AB-</option>
        </select>

        <label>Chronic Diseases</label>
        <input type="text" name="chronic_diseases" placeholder="List or type 'None'">

        <label>Medications</label>
        <input type="text" name="medications" placeholder="List or type 'None'">

        <label>Do you smoke?</label>
        <select name="smoking_status">
          <option value="No">No</option>
          <option value="Yes">Yes</option>
        </select>

        <label>Do you consume alcohol?</label>
        <select name="alcohol_consumption">
          <option value="No">No</option>
          <option value="Yes">Yes</option>
        </select>

        <label>Are you pregnant? (Only if applicable)</label>
        <select name="pregnancy_status">
          <option value="No">No</option>
          <option value="Yes">Yes</option>
          <option value="N/A">Not Applicable</option>
        </select>
      </div>

      <br>
      <button type="submit">Submit 🩸</button>
    </form>
  </div>
</body>
</html>
