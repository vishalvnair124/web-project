<?php
session_start();
require_once '../common/connection.php';

if (!isset($_SESSION['user_email'])) {
    die("Unauthorized access");
}

$email = $_SESSION['user_email'];

// Kochi Coordinates
$kochilat = 9.9312;
$kochilon = 76.2673;

function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371;
    $dlat = deg2rad($lat2 - $lat1);
    $dlon = deg2rad($lon2 - $lon1);

    $a = sin($dlat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dlon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = round($earthRadius * $c);

    if ($lat2 < $lat1 || $lon2 < $lon1) {
        $distance *= -1;
    }

    return $distance;
}

// Form inputs
$name = $_POST['fullname'] ?? '';
$phone = $_POST['phone'] ?? '';
$gender = $_POST['gender'] ?? '';
$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;
$blood_group = $_POST['blood_group'] ?? '';
$availability_status = $_POST['interested'] ?? 0;

// Distance
$user_distance = null;
if (!empty($latitude) && !empty($longitude)) {
    $user_distance = haversineDistance($kochilat, $kochilon, $latitude, $longitude);
}

// Get current user info
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$current = $result->fetch_assoc();
$stmt->close();

// Dynamic updates
$fields = [];
$params = [];
$types = '';

function addFieldIfChanged($key, $newValue, $currentValue, &$fields, &$params, &$types)
{
    if ($newValue !== '' && $newValue !== $currentValue && $newValue !== null) {
        $fields[] = "$key = ?";
        $params[] = $newValue;
        $types .= is_numeric($newValue) ? 'd' : 's';
    }
}

addFieldIfChanged("name", $name, $current['name'], $fields, $params, $types);
addFieldIfChanged("phone", $phone, $current['phone'], $fields, $params, $types);
addFieldIfChanged("user_gender", $gender, $current['user_gender'], $fields, $params, $types);
addFieldIfChanged("blood_group", $blood_group, $current['blood_group'], $fields, $params, $types);
addFieldIfChanged("latitude", $latitude, $current['latitude'], $fields, $params, $types);
addFieldIfChanged("longitude", $longitude, $current['longitude'], $fields, $params, $types);
addFieldIfChanged("user_distance", $user_distance, $current['user_distance'], $fields, $params, $types);
addFieldIfChanged("availability_status", $availability_status, $current['availability_status'], $fields, $params, $types);

// Update users table
if (count($fields) > 0) {
    $query = "UPDATE users SET " . implode(", ", $fields) . " WHERE email = ?";
    $params[] = $email;
    $types .= 's';
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();
}

// Health Info
if (!empty($_POST['weight'])) {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        $check = $conn->prepare("SELECT donor_info_id FROM donor_info WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $check->store_result();

        $pregnancy_map = [
            'Pregnant' => 'Yes',
            'Not Pregnant' => 'No',
            'Not Applicable' => 'N/A'
        ];
        $pregnancy_input = $_POST['pregnancy_status'] ?? 'Not Applicable';
        $pregnancy_status = $pregnancy_map[$pregnancy_input] ?? 'N/A';

        // Sanitize health fields
        $weight = floatval($_POST['weight']);
        $height = floatval($_POST['height']);
        $pulse_rate = intval($_POST['pulse_rate']);
        $user_dob = $_POST['user_dob'] ?? '';
        $birthdate = date('Y-m-d', strtotime($user_dob));
        $body_temp = floatval($_POST['body_temperature']);
        $bp = $_POST['blood_pressure'] ?? '';
        $hb = floatval($_POST['hemoglobin_level']);
        $chol = floatval($_POST['cholesterol']);
        $last_donation = $_POST['last_donation_date'] ?? null;
        if (!empty($last_donation)) {
            $last_donation = date('Y-m-d', strtotime($last_donation));
        }
        $diseases = $_POST['chronic_diseases'] ?? '';
        $medications = $_POST['medications'] ?? '';
        $alcohol = $_POST['alcohol_consumption'] ?? '';
        $tattoos = $_POST['tattoos_piercings'] ?? '';

        if ($check->num_rows > 0) {
            // UPDATE
            $stmt = $conn->prepare("UPDATE donor_info SET 
                user_dob=?, weight=?, height=?, pulse_rate=?, body_temperature=?,
                blood_pressure=?, hemoglobin_level=?, cholesterol=?, last_donation_date=?,
                chronic_diseases=?, medications=?, alcohol_consumption=?, tattoos_piercings=?, pregnancy_status=?
                WHERE user_id=?");

            $stmt->bind_param(
                "sddidddsisssssi",
                $birthdate,
                $weight,
                $height,
                $pulse_rate,
                $body_temp,
                $bp,
                $hb,
                $chol,
                $last_donation,
                $diseases,
                $medications,
                $alcohol,
                $tattoos,
                $pregnancy_status,
                $user_id
            );
        } else {
            // INSERT
            $stmt = $conn->prepare("INSERT INTO donor_info (
                user_id, user_dob, weight, height, pulse_rate, body_temperature,
                blood_pressure, hemoglobin_level, cholesterol, last_donation_date,
                chronic_diseases, medications, alcohol_consumption, tattoos_piercings, pregnancy_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "isddidddsisssss",
                $user_id,
                $birthdate,
                $weight,
                $height,
                $pulse_rate,
                $body_temp,
                $bp,
                $hb,
                $chol,
                $last_donation,
                $diseases,
                $medications,
                $alcohol,
                $tattoos,
                $pregnancy_status
            );
        }

        $stmt->execute();
        $stmt->close();
        $check->close();
    }
}

echo "<script>alert('Profile updated successfully!'); window.location.href='../user/index.php';</script>";
