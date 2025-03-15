<?php
include '../common/session_check.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drop4Life🩸| <?php $_SESSION["user"]; ?></title>
    <link rel="stylesheet" href="./styles/newstyle.css">
    <link rel="stylesheet" href="./styles/responsive.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for easier AJAX handling -->
</head>

<body>

    <!-- Header -->
    <header>
        <div class="logosec">
            <div class="logo">Drop4Life🩸</div>
            <img src="../media/icon-menu.png" class="icn menuicn" id="menuicn" alt="menu-icon">
        </div>
        <div class="chat_img">
            <a href=""><img src="../media/chat.png" alt="" style="max-width: 50px;
        height: 35px;"></a>
        </div>
    </header>

    <div class="main-container">
        <!-- Sidebar Navigation -->
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <a href="?page=dashboard.php" class="nav-option option1 no-a" data-page="dashboard.php">
                        <img src="../media/dashboard.png" class="nav-img" alt="dashboard">
                        <h3>Dashboard</h3>
                    </a>
                    <a href="?page=requests.php" class="nav-option no-a" data-page="requests.php">
                        <img src="../media/request.png" class="nav-img" alt="requests">
                        <h3>Requests</h3>
                    </a>
                    <a href="?page=testimonials.php" class="nav-option no-a" data-page="testimonials.php">
                        <img src="../media/testimonial.png" class="nav-img" alt="testimonials">
                        <h3>Donation</h3>
                    </a>
                    <a href="?page=users.php" class="nav-option no-a" data-page="users.php">
                        <img src="../media/users.png" class="nav-img" alt="users">
                        <h3>My chats</h3>
                    </a>
                    <a href="?page=enquiry.php" class="nav-option no-a" data-page="enquiry.php">
                        <img src="../media/enquiry.png" class="nav-img" alt="users">
                        <h3>Enquiry</h3>
                    </a>
                    <a href="?page=settings.php" class="nav-option no-a" data-page="settings.php">
                        <img src="../media/settings_icon.png" class="nav-img" alt="settings">
                        <h3>Settings</h3>
                    </a>
                    <a href="logout.php" class="no-a">
                        <div class="nav-option logout" data-page="logout.php">
                            <img src="../media/logout_icon.png" class="nav-img" alt="logout">
                            <h3>Logout</h3>
                        </div>
                    </a>
                </div>
            </nav>
        </div>


        <!-- Main Content -->
        <div class="main">
            <div id="content"></div> <!-- Dynamic content will be loaded here -->
        </div>
    </div>

    <script src="index.js"></script> <!-- Separate JS file for handling dynamic loading and other interactions -->
</body>

</html>