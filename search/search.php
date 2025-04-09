<?php
require '../common/connection.php'; // Include database connection

// Kochi's Latitude and Longitude (Center Point)
$kochilat = 9.9312;
$kochilon = 76.2673;

// Get recipient's request ID from the GET request
$request_id = $_GET['request_id'] ?? 0;

// Validate request ID
if (!$request_id) {
    die(json_encode(["error" => "Invalid request ID."]));
}

// Fetch request details (latitude, longitude, blood group, request level) from blood_requests table
$query = "SELECT latitude, longitude, blood_group, request_level FROM blood_requests WHERE request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $request_id); // Bind request ID as an integer
$stmt->execute();
$stmt->bind_result($recipient_lat, $recipient_lon, $recipient_blood_group, $level);
$stmt->fetch();
$stmt->close();

// Check if a valid request was found
if (!$recipient_lat || !$recipient_lon || !$recipient_blood_group) {
    die(json_encode(["error" => "No data found for this request ID."]));
}

// Calculate distance from Kochi to recipient using Haversine formula
$distance_from_kochilat = haversineDistance($kochilat, $kochilon, $recipient_lat, $recipient_lon);

// Set the limit of donors based on request level
$limit = $level * 10; // Level 1 → 10 donors, Level 2 → 20 donors

// Find available donors within the required distance range
$donors = getDonorsWithinDistance($conn, $recipient_blood_group, $distance_from_kochilat, $limit, $request_id);

// Return results as a JSON response
header('Content-Type: application/json');
echo json_encode($donors);

/**
 * Function to calculate the Haversine distance between two geographical points
 *
 * @param float $lat1 Latitude of point 1
 * @param float $lon1 Longitude of point 1
 * @param float $lat2 Latitude of point 2
 * @param float $lon2 Longitude of point 2
 * @return float Distance in kilometers
 */
function haversineDistance($lat1, $lon1, $lat2, $lon2)
{
    $earth_radius = 6371; // Earth radius in kilometers

    // Convert latitude and longitude differences to radians
    $dlat = deg2rad($lat2 - $lat1);
    $dlon = deg2rad($lon2 - $lon1);

    // Apply Haversine formula
    $a = sin($dlat / 2) * sin($dlat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dlon / 2) * sin($dlon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earth_radius * $c; // Distance in kilometers
}

/**
 * Function to find exactly (request_level * 10) donors, excluding already-notified ones
 *
 * @param mysqli $conn Database connection
 * @param string $blood_group Required blood group
 * @param float $initial_distance Initial distance from Kochi
 * @param int $limit Number of donors to fetch
 * @param int $request_id Request ID to exclude previously notified donors
 * @return array List of matching donors
 */
function getDonorsWithinDistance($conn, $blood_group, $initial_distance, $limit, $request_id)
{
    $donors = []; // Store matched donors
    $search_distance = $initial_distance;
    $increment = 5; // Expand search range if not enough donors found

    while (count($donors) < $limit) {
        // Query to find donors within the search distance who were not already notified for this request
        $sql = "SELECT u.user_id, u.name, u.blood_group, u.user_distance
                FROM users u
                WHERE u.availability_status = 1
                  AND u.blood_group = ?
                  AND ABS(u.user_distance - ?) <= 2
                  AND NOT EXISTS (
                      SELECT 1 FROM donor_notifications dn
                      WHERE dn.donor_id = u.user_id AND dn.request_id = ?
                  )
                ORDER BY u.user_distance ASC
                LIMIT ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdii", $blood_group, $search_distance, $request_id, $limit); // Updated to 4 params
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $donors[] = $row;
            if (count($donors) == $limit) break;
        }

        $stmt->close();

        if (count($donors) >= $limit) {
            break;
        }

        $search_distance += $increment;
    }

    return array_slice($donors, 0, $limit); // Return only the required number
}
