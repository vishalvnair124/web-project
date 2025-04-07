<?php
// Include session check to ensure the user is authenticated
include '../common/session_check.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drop4Life🩸| <?php $_SESSION["user"]; ?></title> <!-- Display the user's session name in the title -->
    <link rel="stylesheet" href="./styles/newstyle.css"> <!-- Main stylesheet -->
    <link rel="stylesheet" href="./styles/responsive.css"> <!-- Responsive stylesheet for mobile devices -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for easier AJAX handling -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" /><!-- Leaflet CSS for map display -->
</head>

<body>

    <!-- Header Section -->
    <header>
        <div class="logosec">
            <div class="logo">Drop4Life🩸</div> <!-- Logo of the application -->
            <img src="../media/icon-menu.png" class="icn menuicn" id="menuicn" alt="menu-icon"> <!-- Menu icon for toggling navigation -->
        </div>
        <div class="chat_img">
            <!-- Link to the chat list screen -->
            <a href="../user/chatlistscreen.php">
                <img src="../media/chat.png" alt="Chat" style="max-width: 50px; height: 35px;">
            </a>
        </div>
    </header>

    <div class="main-container">
        <!-- Sidebar Navigation -->
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <!-- Dashboard Navigation Option -->
                    <a href="?page=dashboard.php" class="nav-option option1 no-a" data-page="dashboard.php">
                        <img src="../media/dashboard.png" class="nav-img" alt="dashboard">
                        <h3>Dashboard</h3>
                    </a>
                    <!-- Requests Navigation Option -->
                    <a href="?page=requests.php" class="nav-option no-a" data-page="requests.php">
                        <img src="../media/request.png" class="nav-img" alt="requests">
                        <h3>Requests</h3>
                    </a>
                    <!-- Donation Navigation Option -->
                    <a href="?page=donations.php" class="nav-option no-a" data-page="donations.php">
                        <img src="../media/blood-donation.png" class="nav-img" alt="donations">
                        <h3>Donations</h3>
                    </a>

                    <!-- Settings Navigation Option -->
                    <a href="?page=settings.php" class="nav-option no-a" data-page="settings.php">
                        <img src="../media/settings_icon.png" class="nav-img" alt="settings">
                        <h3>Settings</h3>
                    </a>
                    <!-- Logout Option -->
                    <a href="logout.php" class="no-a">
                        <div class="nav-option logout" data-page="logout.php">
                            <img src="../media/logout_icon.png" class="nav-img" alt="logout">
                            <h3>Logout</h3>
                        </div>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content Section -->
        <div class="main">
            <div id="content"></div> <!-- Dynamic content will be loaded here via AJAX -->
        </div>
    </div>

    <!-- JavaScript File -->
    <script src="index.js"></script> <!-- Separate JS file for handling dynamic loading and other interactions -->
</body>

</html>