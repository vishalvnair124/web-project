<?php
session_start();

// 🔗 DB CONNECTION
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dropforlife";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ DB Connection failed: " . $conn->connect_error);
}

// 🚫 OTP Check
if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header("Location: otpscreen.php?error=unauthorized");
    exit();
}

$email = $_SESSION['user_email'];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // 📦 Get form values
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
    $blood_group = $_GET['blood_group'];

    // 👀 Check if user already exists
    $checkUser = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkUser->bind_param("s", $email);
    $checkUser->execute();
    $result = $checkUser->get_result();

    // ➕ Insert new user if doesn't exist
    if ($result->num_rows === 0) {
        $insertUser = $conn->prepare("INSERT INTO users (name, user_gender, email, phone, blood_group) VALUES (?, ?, ?, ?, ?)");
        $insertUser->bind_param("sssss", $fullname, $gender, $email, $phone, $blood_group);
        $insertUser->execute();
    }

    // 📥 Get user_id
    $getUserID = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $getUserID->bind_param("s", $email);
    $getUserID->execute();
    $userResult = $getUserID->get_result();
    $user = $userResult->fetch_assoc();
    $user_id = $user['user_id'];

    // ❤️ If interested in donation
    if ($interested === "Yes") {
        $weight = $_GET['weight'];
        $height = $_GET['height'];
        $blood_pressure = $_GET['blood_pressure'];
        $pulse_rate = $_GET['pulse_rate'];
        $body_temp = $_GET['body_temperature'];
        $hemoglobin = $_GET['hemoglobin_level'];
        $cholesterol = $_GET['cholesterol'];
        $last_donation_date = $_GET['last_donation_date'];
        $chronic = $_GET['chronic_diseases'];
        $medications = $_GET['medications'];
        $alcohol = $_GET['alcohol_consumption'];
        $tattoos = $_GET['tattoos_piercings'];
        $pregnancy = $_GET['pregnancy_status'];

        $stmt = $conn->prepare("INSERT INTO donor_info (
            user_id, weight, height, blood_pressure, pulse_rate, body_temperature, 
            hemoglobin_level, cholesterol, last_donation_date, chronic_diseases, medications,
            alcohol_consumption, tattoos_piercings, pregnancy_status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        if (!$stmt) {
            die("❌ Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("idssidddssssss",
            $user_id, $weight, $height, $blood_pressure, $pulse_rate, $body_temp,
            $hemoglobin, $cholesterol, $last_donation_date, $chronic, $medications,
            $alcohol, $tattoos, $pregnancy
        );

        if ($stmt->execute()) {
            echo "<h2>✅ Donor information saved successfully!</h2>";
        } else {
            echo "❌ Error inserting donor info: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "<h2>✅ Registered successfully (not interested in donating blood)</h2>";
    }
}

$conn->close();
?>
