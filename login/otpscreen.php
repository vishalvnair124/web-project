<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Enter OTP - Drop4Life🩸</title>
    <link rel="stylesheet" href="newstyle.css" /> <!-- Add your external CSS here if needed -->
</head>
<?php
if (isset($_GET['email'])) {
    $email = $_GET['email'];  // Get the email from URL
    
} else {
   // echo "No email found in URL.";
}
?>



<body>
    <div class="container">
        <h2>Enter OTP 🩸</h2>

        <!-- Simple OTP Form -->
        <form action="verifyotp.php" method="post">
            <div class="input-group">
                
                <input type="text" name="otp" id="otp" maxlength="6" />
            </div>
              <input type="hidden" name="email" value="<?php echo $email; ?>">
            <button type="submit" class="btn">Verify OTP 🩸</button>
            
        </form>
    </div>
</body>

</html>
