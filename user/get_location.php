<?php
include '../common/config.php'; // Secure API Key

if (isset($_GET['lat']) && isset($_GET['lon'])) {
    $latitude = $_GET['lat'];
    $longitude = $_GET['lon'];

    $apiKey = GEOAPIFY_API_KEY;
    $url = "https://api.geoapify.com/v1/geocode/reverse?lat=$latitude&lon=$longitude&apiKey=$apiKey";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (!empty($data['features'][0]['properties'])) {
        $state = $data['features'][0]['properties']['state'] ?? "Unknown";
        $county = $data['features'][0]['properties']['county'] ?? "Unknown";
        $city = $data['features'][0]['properties']['city'] ?? "Unknown";
        $place = "$city, $county, $state";
    } else {
        $place = "Unknown Location";
    }

    echo json_encode(["place" => $place]);
} else {
    echo json_encode(["error" => "Invalid parameters"]);
}
