<?php
session_start();

// 🔗 DATABASE CONNECTION
$servername = "localhost";
$username = "root";
$password = ""; // or your actual DB password
$dbname = "dropforlife"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// ❌ Connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ OTP Check
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header("Location: otpscreen.php?error=unauthorized");
    exit();
}

$email = $_SESSION['user_email'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // 📦 User data
    $fullname = $_GET['fullname'];
    $phone = $_GET['phone'];
    $birthdate = $_GET['birthdate'];
    $gender = $_GET['gender'];
    $address1 = $_GET['address1'];
    $address2 = $_GET['address2'];
    $district = $_GET['district'];
    $state = $_GET['state'];
    $postal = $_GET['postal'];
    $interested = $_GET['interested'];
    $blood_group = isset($_GET['blood_group']) ? $_GET['blood_group'] : null;

    // 🔍 Check if user exists
    $checkUser = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    // 🧑 Insert new user if not exists
    if ($result->num_rows == 0) {
        $insertUser = $conn->prepare("INSERT INTO users (name, user_gender, email, phone, blood_group) VALUES (?, ?, ?, ?, ?)");
        $insertUser->bind_param("sssss", $fullname, $gender, $email, $phone, $blood_group);
        $insertUser->execute();
    }

    // 🔁 Get user_id
    $getUserID = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $getUserID->bind_param("s", $email);
    $getUserID->execute();
    $userResult = $getUserID->get_result();
    $user = $userResult->fetch_assoc();
    $user_id = $user['user_id'];

    // ❤️ If interested, insert into donor_info
    if ($interested === "Yes") {
        $weight = $_GET['weight'];
        $blood_pressure = $_GET['blood_pressure'];
        $hemoglobin = $_GET['hemoglobin_level'];
        $chronic = $_GET['chronic_diseases'];
        $medications = $_GET['medications'];
        $smoking = $_GET['smoking_status'];
        $alcohol = $_GET['alcohol_consumption'];
        $pregnancy = $_GET['pregnancy_status'];

        $insertDonor = $conn->prepare("INSERT INTO donor_info (user_id, weight, blood_pressure, hemoglobin_level, chronic_diseases, medications, smoking_status, alcohol_consumption, pregnancy_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $insertDonor->bind_param("idsssssss", $user_id, $weight, $blood_pressure, $hemoglobin, $chronic, $medications, $smoking, $alcohol, $pregnancy);
        $insertDonor->execute();
    }

    echo "<h2>✅ Registration Successful!</h2>";
    // Optional: Redirect to success page
    // header("Location: success.php");
}

$conn->close();
?>
