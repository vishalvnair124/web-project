<?php
session_start();
require_once '../common/connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    die("Unauthorized access"); // Terminate if the user is not logged in
}

$email = $_SESSION['user_email']; // Get the logged-in user's email

// Kochi Coordinates (used for distance calculation)
$kochilat = 9.9312;
$kochilon = 76.2673;

// Function to calculate the Haversine distance between two coordinates
function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // Radius of the Earth in kilometers
    $dlat = deg2rad($lat2 - $lat1); // Difference in latitude
    $dlon = deg2rad($lon2 - $lon1); // Difference in longitude

    // Haversine formula
    $a = sin($dlat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dlon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = round($earthRadius * $c); // Calculate the distance

    // Adjust the distance based on coordinate direction
    if ($lat2 < $lat1 || $lon2 < $lon1) {
        $distance *= -1; // Reverse the distance if coordinates are in the opposite direction
    }

    return $distance; // Return the calculated distance
}

// Retrieve form inputs
$name = $_POST['fullname'] ?? ''; // Full name of the user
$phone = $_POST['phone'] ?? ''; // Phone number of the user
$gender = $_POST['gender'] ?? ''; // Gender of the user
$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null; // Latitude of the user
$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null; // Longitude of the user
$blood_group = $_POST['blood_group'] ?? ''; // Blood group of the user
$availability_status = $_POST['availability_status'] ?? 0; // Availability status of the user
$password = ($_POST['pass'] == "") ? $_POST['passTemp'] : password_hash($_POST['pass'], PASSWORD_DEFAULT); // Password (hashed if provided)

// Calculate the user's distance from Kochi
$user_distance = null;
if (!empty($latitude) && !empty($longitude)) {
    $user_distance = haversineDistance($kochilat, $kochilon, $latitude, $longitude); // Calculate distance
}

// Fetch the current user information from the database
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$current = $result->fetch_assoc(); // Fetch the current user data
$stmt->close();

// Prepare dynamic updates for the `users` table
$fields = []; // Array to store fields to be updated
$params = []; // Array to store parameter values
$types = ''; // String to store parameter types

// Function to add a field to the update query if its value has changed
function addFieldIfChanged($key, $newValue, $currentValue, &$fields, &$params, &$types)
{
    if ($newValue !== '' && $newValue !== $currentValue && $newValue !== null) {
        $fields[] = "$key = ?"; // Add the field to the update query
        $params[] = $newValue; // Add the new value to the parameters array
        $types .= is_numeric($newValue) ? 'd' : 's'; // Determine the parameter type
    }
}

// Add fields to the update query if they have changed
addFieldIfChanged("name", $name, $current['name'], $fields, $params, $types);
addFieldIfChanged("phone", $phone, $current['phone'], $fields, $params, $types);
addFieldIfChanged("user_gender", $gender, $current['user_gender'], $fields, $params, $types);
addFieldIfChanged("blood_group", $blood_group, $current['blood_group'], $fields, $params, $types);
addFieldIfChanged("latitude", $latitude, $current['latitude'], $fields, $params, $types);
addFieldIfChanged("longitude", $longitude, $current['longitude'], $fields, $params, $types);
addFieldIfChanged("user_distance", $user_distance, $current['user_distance'], $fields, $params, $types);
addFieldIfChanged("availability_status", $availability_status, $current['availability_status'], $fields, $params, $types);
addFieldIfChanged("password", $password, $current['password'], $fields, $params, $types);

// Update the `users` table if there are changes
if (count($fields) > 0) {
    $query = "UPDATE users SET " . implode(", ", $fields) . " WHERE email = ?"; // Construct the update query
    $params[] = $email; // Add the email to the parameters array
    $types .= 's'; // Add the parameter type for the email
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params); // Bind the parameters
    $stmt->execute(); // Execute the update query
    $stmt->close();
}

// Process health information if provided
if (!empty($_POST['weight'])) {
    // Fetch the user ID based on the email
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch(); // Fetch the user ID
    $stmt->close();

    if ($user_id) {
        // Check if the user already has health information in the `donor_info` table
        $check = $conn->prepare("SELECT donor_info_id FROM donor_info WHERE user_id = ?");
        $check->bind_param("i", $user_id);
        $check->execute();
        $check->store_result();

        // Map pregnancy status input to database values
        $pregnancy_map = [
            'Pregnant' => 'Yes',
            'Not Pregnant' => 'No',
            'Not Applicable' => 'N/A'
        ];
        $pregnancy_input = $_POST['pregnancy_status'] ?? 'Not Applicable'; // Get pregnancy status input
        $pregnancy_status = $pregnancy_map[$pregnancy_input] ?? 'N/A'; // Map input to database value

        // Sanitize health-related fields
        $weight = floatval($_POST['weight']); // Weight of the user
        $height = floatval($_POST['height']); // Height of the user
        $pulse_rate = intval($_POST['pulse_rate']); // Pulse rate of the user
        $user_dob = $_POST['user_dob'] ?? ''; // Date of birth of the user
        $birthdate = date('Y-m-d', strtotime($user_dob)); // Format the date of birth
        $body_temp = floatval($_POST['body_temperature']); // Body temperature of the user
        $bp = $_POST['blood_pressure'] ?? ''; // Blood pressure of the user
        $hb = floatval($_POST['hemoglobin_level']); // Hemoglobin level of the user
        $chol = floatval($_POST['cholesterol']); // Cholesterol level of the user
        $last_donation = $_POST['last_donation_date'] ?? null; // Last donation date
        if (!empty($last_donation)) {
            $last_donation = date('Y-m-d', strtotime($last_donation)); // Format the last donation date
        }
        $diseases = $_POST['chronic_diseases'] ?? ''; // Chronic diseases of the user
        $medications = $_POST['medications'] ?? ''; // Medications of the user
        $alcohol = $_POST['alcohol_consumption'] ?? ''; // Alcohol consumption status
        $tattoos = $_POST['tattoos_piercings'] ?? ''; // Tattoos or piercings status

        if ($check->num_rows > 0) {
            // Update existing health information
            $stmt = $conn->prepare("UPDATE donor_info SET 
                user_dob=?, weight=?, height=?, pulse_rate=?, body_temperature=?,
                blood_pressure=?, hemoglobin_level=?, cholesterol=?, 
                chronic_diseases=?, medications=?, alcohol_consumption=?, tattoos_piercings=?, pregnancy_status=?
                WHERE user_id=?");

            $stmt->bind_param(
                "sddidsdsissssi",
                $birthdate,
                $weight,
                $height,
                $pulse_rate,
                $body_temp,
                $bp,
                $hb,
                $chol,
                $diseases,
                $medications,
                $alcohol,
                $tattoos,
                $pregnancy_status,
                $user_id
            );
        } else {
            // Insert new health information
            $stmt = $conn->prepare("INSERT INTO donor_info (
                user_id, user_dob, weight, height, pulse_rate, body_temperature,
                blood_pressure, hemoglobin_level, cholesterol,
                chronic_diseases, medications, alcohol_consumption, tattoos_piercings, pregnancy_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "isddidsdsissss",
                $user_id,
                $birthdate,
                $weight,
                $height,
                $pulse_rate,
                $body_temp,
                $bp,
                $hb,
                $chol,
                $diseases,
                $medications,
                $alcohol,
                $tattoos,
                $pregnancy_status
            );
        }

        $stmt->execute(); // Execute the query
        $stmt->close(); // Close the statement
        $check->close(); // Close the check statement
    }
}

// Redirect the user with a success message
echo "<script>alert('Profile updated successfully!'); window.location.href='../user/index.php?page=settings.php';</script>";
