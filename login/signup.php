<?php
session_start();

// Include the database connection
include '../common/connection.php';
require '../phpmailer/phpmailer/src/PHPMailer.php';
require '../phpmailer/phpmailer/src/SMTP.php';
require '../phpmailer/phpmailer/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Handle the first step - OTP generation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['email'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Check if the email already exists
    $checkEmailSql = "SELECT email FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($checkEmailSql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Email already exists - Redirect to index with error
            header("Location: index.php?signup=email_exists");
            exit();
        } else {
            // Generate a 6-digit OTP
            $otp = rand(100000, 999999);

            // Save OTP in password column and set user_status = 0 (pending)
            $insertSql = "INSERT INTO users (name, email, password, user_status) VALUES (?, ?, ?, 0)";
            if ($stmt = $conn->prepare($insertSql)) {
                $stmt->bind_param("sss", $name, $email, $otp);
                if ($stmt->execute()) {
                    $_SESSION['email'] = $email;
                    $_SESSION['otp'] = $otp;

                    // Send OTP to the user's email
                    if (sendOtpEmail($email, $name, $otp)) {
                        // Redirect to the same page for OTP verification
                        header("Location: signup.php?otp_verification=true");
                        exit();
                    } else {
                        header("Location: index.php?signup=otp_failed");
                        exit();
                    }
                }
            }
        }
    }
}

// Handle OTP verification after form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    $entered_otp = $_POST['otp'];
    $email = $_SESSION['email'];

    // Check if the entered OTP matches
    $checkOtpSql = "SELECT user_id FROM users WHERE email = ? AND password = ?";
    if ($stmt = $conn->prepare($checkOtpSql)) {
        $stmt->bind_param("ss", $email, $entered_otp);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // OTP matched - Update user_status and clear OTP from password column
            $updateStatusSql = "UPDATE users SET user_status = 1, password = '' WHERE email = ?";
            if ($stmt = $conn->prepare($updateStatusSql)) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                // Redirect to success page
                header("Location: index.php?signup=success");
                exit();
            }
        } else {
            // Invalid OTP - Show error
            header("Location: signup.php?otp_verification=true&error=invalid_otp");
            exit();
        }
    }
}

// Function to send OTP using PHPMailer
function sendOtpEmail($recipient, $name, $otp)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                      // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                 // SMTP server
        $mail->SMTPAuth   = true;                             // Enable SMTP authentication
        $mail->Username   = 'your-email@gmail.com';           // Your Gmail ID
        $mail->Password   = 'your-app-password';              // App password, NOT Gmail password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption
        $mail->Port       = 587;                              // Port 587 for TLS
    
        // Sender and recipient settings
        $mail->setFrom('your-email@gmail.com', 'Drop For Life');
        $mail->addAddress($email, $name);                     // Recipient email and name
    
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Registration';
        $mail->Body    = "Hi $name,<br>Your OTP for registration is: <strong>$otp</strong><br>Please enter this OTP to complete your registration.";
        
        // Send the email
        if ($mail->send()) {
            // Redirect to OTP verification screen after email is sent
            header("Location: verify_otp.php?email=" . urlencode($email));
            exit();
        } else {
            header("Location: index.php?signup=otp_failed");
            exit();
        }
    } catch (Exception $e) {
        echo "Error while sending email: {$mail->ErrorInfo}";
    }
}   
   ?>  

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Drop4Life</title>
    <link rel="stylesheet" href="../newstyle.css">
</head>

<body>
    <div class="wrapper">
        <?php if (isset($_GET['otp_verification']) && $_GET['otp_verification'] == 'true') : ?>
            <!-- OTP Verification Form -->
            <div class="form-wrapper">
                <form action="signup.php" method="post">
                    <h2>Verify OTP 🔐</h2>
                    <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid_otp') : ?>
                        <p style="color: red;">Invalid OTP. Please try again!</p>
                    <?php endif; ?>
                    <div class="input-group">
                        <input type="text" name="otp" required />
                        <label for="otp">Enter OTP</label>
                    </div>
                    <button type="submit" name="verify_otp" class="btn">Verify OTP 🔒</button>
                </form>
            </div>
        <?php else : ?>
            <!-- Sign Up Form -->
            <div class="form-wrapper">
                <form action="signup.php" method="post">
                    <h2>Sign Up 🩸</h2>
                    <div class="input-group">
                        <input type="text" name="name" required />
                        <label for="name">Name</label>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" required />
                        <label for="email">Email</label>
                    </div>
                    <button type="submit" class="btn">Get OTP ✉️</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
