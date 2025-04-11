<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Enter OTP - Drop4Life</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(to bottom, #e91e63, #2196f3);
        }

        .otp-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .otp-container h2 {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
        }

        .emoji {
            color: red;
            margin-left: 8px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #aaa;
            font-size: 16px;
            margin-bottom: 25px;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to left, #e91e63, #2196f3);
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .btn .emoji {
            font-size: 18px;
            vertical-align: middle;
            margin-left: 5px;
        }
    </style>
</head>

<?php
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

$error = isset($_GET['error']) ? $_GET['error'] : '';
$errorMessage = '';

switch ($error) {
    case 'invalid_otp':
        $errorMessage = 'Invalid OTP. Please try again.';
        break;
    case 'no_user':
        $errorMessage = 'No user found with this email.';
        break;
        // Add more error cases here if needed
}
?>

<body>
    <div class="otp-container">
        <h2>Enter OTP <span class="emoji">🩸</span></h2>

        <?php if ($error): ?>
            <p style="color: red; margin-bottom: 15px;">❌ <?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form action="verifyotp.php" method="post">
            <input type="text" name="otp" placeholder="Enter OTP" maxlength="6" required>
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            <button type="submit" class="btn">Verify OTP <span class="emoji">🩸</span></button>
        </form>
    </div>
</body>

</html>