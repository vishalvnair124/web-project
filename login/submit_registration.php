<?php
session_start();
require_once '../common/connection.php';

if (!isset($_SESSION['user_email'])) {
    die("Unauthorized access");
}

$email = $_SESSION['user_email'];

// Get form values
$name = $_POST['fullname'];
$phone = $_POST['phone'];
$birthdate = $_POST['birthdate'];
$gender = $_POST['gender'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$blood_group = $_POST['blood_group'];
$Pass = password_hash($_POST['pass'], PASSWORD_DEFAULT);
$interested = (strtolower(trim($_POST['interested'])) === 'yes') ? 1 : 0;

// Kochi Coordinates
$kochilat = 9.9312;
$kochilon = 76.2673;

// Haversine distance formula
function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    try {
        $earthRadius = 6371;
        $lat1_rad = deg2rad($lat1);
        $lon1_rad = deg2rad($lon1);
        $lat2_rad = deg2rad($lat2);
        $lon2_rad = deg2rad($lon2);

        $dlat = $lat2_rad - $lat1_rad;
        $dlon = $lon2_rad - $lon1_rad;

        $a = sin($dlat / 2) ** 2 + cos($lat1_rad) * cos($lat2_rad) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = round($earthRadius * $c);

        // Mark as negative if user is behind Kochi
        if ($lat2 < $lat1 || $lon2 < $lon1) {
            $distance *= -1;
        }

        return $distance;
    } catch (Execption | Error $e) {
        return 0;
    }
}

$user_distance = haversineDistance($kochilat, $kochilon, $latitude, $longitude);

try {
    // Update user data
    $status = 1;
    $stmt = $conn->prepare("UPDATE users SET name=?,password=?, phone=?, user_gender=?, latitude=?, longitude=?, availability_status=?, user_distance=?, blood_group=?,user_status=? WHERE email=?");
    $stmt->bind_param("sssssssisis", $name, $Pass, $phone, $gender, $latitude, $longitude, $interested, $user_distance, $blood_group, $status, $email);
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    if ($interested === 1) {
        // Get user_id


        if ($user_id) {
            // Check if donor_info exists
            $check = $conn->prepare("SELECT donor_info_id FROM donor_info WHERE user_id = ?");
            $check->bind_param("i", $user_id);
            $check->execute();
            $check->store_result();
            $pregnancy_map = [
                'Pregnant' => 'Yes',
                'Not Pregnant' => 'No',
                'Not Applicable' => 'N/A'
            ];

            $pregnancy_status_input = $_POST['pregnancy_status'];
            $pregnancy_status = isset($pregnancy_map[$pregnancy_status_input]) ? $pregnancy_map[$pregnancy_status_input] : 'N/A';


            if ($check->num_rows > 0) {
                // Update donor_info
                $stmt = $conn->prepare("UPDATE donor_info SET 
                    user_dob=?, weight=?, height=?, pulse_rate=?, body_temperature=?,
                    blood_pressure=?, hemoglobin_level=?, cholesterol=?,
                    chronic_diseases=?, medications=?, alcohol_consumption=?, tattoos_piercings=?, pregnancy_status=?
                    WHERE user_id=?");

                $stmt->bind_param(
                    "sddidsddsssssi",
                    $birthdate,
                    $_POST['weight'],
                    $_POST['height'],
                    $_POST['pulse_rate'],
                    $_POST['body_temperature'],
                    $_POST['blood_pressure'],
                    $_POST['hemoglobin_level'],
                    $_POST['cholesterol'],
                    $_POST['chronic_diseases'],
                    $_POST['medications'],
                    $_POST['alcohol_consumption'],
                    $_POST['tattoos_piercings'],
                    $pregnancy_status,
                    $user_id
                );


                $stmt->execute();
                $stmt->close();
            } else {
                // Insert new donor_info
                $created_at = date("Y-m-d H:i:s");

                $stmt = $conn->prepare("INSERT INTO donor_info (
                    user_id, user_dob, weight, height, pulse_rate, body_temperature,
                    blood_pressure, hemoglobin_level, cholesterol, 
                    chronic_diseases, medications, alcohol_consumption, tattoos_piercings, pregnancy_status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $stmt->bind_param(
                    "ssdiddsdsssssss",
                    $user_id,
                    $birthdate,
                    $_POST['weight'],
                    $_POST['height'],
                    $_POST['pulse_rate'],
                    $_POST['body_temperature'],
                    $_POST['blood_pressure'],
                    $_POST['hemoglobin_level'],
                    $_POST['cholesterol'],
                    $_POST['chronic_diseases'],
                    $_POST['medications'],
                    $_POST['alcohol_consumption'],
                    $_POST['tattoos_piercings'],
                    $pregnancy_status,
                    $created_at
                );


                $stmt->execute();
                $stmt->close();
            }

            $check->close();
        }
    } else {
        $created_at = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("INSERT INTO donor_info (
            user_id, user_dob, created_at
        ) VALUES (?, ?, ?)");

        $stmt->bind_param(
            "sss",
            $user_id,
            $birthdate,
            $created_at
        );


        $stmt->execute();
        $stmt->close();
    }
    $_SESSION["user_id"] = $user_id;
    $_SESSION["user"] = $name;
    $_SESSION["isLogined"] = true;
    $_SESSION["user_email"] = $email;
    echo "<script>alert('Registration successful!'); window.location.href='../user/index.php';</script>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
