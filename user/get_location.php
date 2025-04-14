<?php
// Include the configuration file to access the secure API key
include '../common/config.php'; // Secure API Key

// Check if latitude and longitude parameters are provided in the request
if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $latitude = $_GET['lat']; // Get the latitude from the request
    $longitude = $_GET['lon']; // Get the longitude from the request

    // Geoapify API key from the configuration file
    $apiKey = GEOAPIFY_API_KEY;

    // Construct the Geoapify reverse geocoding API URL
    $url = "https://api.geoapify.com/v1/geocode/reverse?lat=$latitude&lon=$longitude&apiKey=$apiKey";

    // Send a GET request to the Geoapify API and decode the JSON response
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    // Check if the response contains location properties
    if (!empty($data['features'][0]['properties'])) {
        // Extract state, county, and city from the response, or set to "Unknown" if not available
        $state = $data['features'][0]['properties']['state'] ?? "Unknown";
        $county = $data['features'][0]['properties']['county'] ?? "Unknown";
        $city = $data['features'][0]['properties']['city'] ?? "Unknown";

        // Combine city, county, and state into a single place string
        $place = "$city, $county, $state";
    } else {
        // If location properties are not available, set the place to "Unknown Location"
        $place = "Unknown Location";
    }

    // Return the place as a JSON response
    echo json_encode(["place" => $place]);
} else {
    // If latitude or longitude parameters are missing, return an error message
    echo json_encode(["error" => "Invalid parameters"]);
}
