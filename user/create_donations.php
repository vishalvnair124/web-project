<?php
// Start session and include necessary files
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in."); // Terminate if user is not logged in
}


?>
<link rel="stylesheet" href="styles/styles.css"> <!-- External CSS -->

<div class="form-container">
    <div class="header">
        <h2>Make Blood Donation</h2>
        <a href="?page=donations.php" class="back-btn">Back</a> <!-- Back button -->
    </div>

    <!-- Form to create a blood request -->
    <form action="process_donation.php" method="POST">
        <label>Request ID </label>
        <input type="text" name="requestid" required>
        <button type="submit" class="submit-btn">Submit Request</button>

        <?php
        if (isset($_GET['error-message'])) {
            $error = urldecode($_GET['error-message']);
            if ($error == 1) {
                $errorMsg = "You cannot donate blood to your own request";
            } else if ($error == 2) {
                $errorMsg = "You are not eligible to donate blood for this request";
            } else if ($error == 3) {
                $errorMsg = "You are not available to donate blood at this time";
            } else if ($error == 4) {
                $errorMsg = "You have already been notified for this request";
            } else if ($error == 5) {
                $errorMsg = "Invalid request ID";
            } else if ($error == 6) {
                $errorMsg = "Request ID already closed or completed";
            } else {
                $errorMsg = "Unknown error occurred";
            }
        }

        if (isset($error)): ?>
            <div class="error-message"><?php echo $errorMsg; ?></div>
        <?php endif; ?>
        <style>
            /* Styling for error messages */
            .error-message {
                color: red;
                text-align: center;
                margin-bottom: 15px;
            }
        </style>
    </form>
</div>