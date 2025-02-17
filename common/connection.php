<?php
$servername = "localhost"; // Your server name or IP address
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "dropforlife"; // Your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    // Redirect to custom 404 error page
    header("Location: 404.html");
    exit();
}

// Check if connection is successful
if (mysqli_connect_errno()) {
    // Output error message and exit if there is an error
    die("Connection failed: " . mysqli_connect_error());
}

//echo "Connected successfully";
