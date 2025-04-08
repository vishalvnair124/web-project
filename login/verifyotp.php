<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../common/connection.php';

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if (isset($_POST['email'], $_POST['otp'])) {
        $email = $_POST['email'];
        $password = $_POST['otp'];

        $sql = "SELECT password FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    // ✅ OTP is correct → Update user_status to 5
                    $updateStatus = "UPDATE users SET user_status = 5 WHERE email = ?";
                    if ($updateStmt = $conn->prepare($updateStatus)) {
                        $updateStmt->bind_param("s", $email);
                        $updateStmt->execute();
                        $updateStmt->close();
                    }

                    // ✅ Start session and store session variables
                    session_start();
                    $_SESSION['user_email'] = $email; // ✅ As you asked
                    $_SESSION['otp_verified'] = true; // ✅ As you asked

                    // ✅ Redirect to registration form
                    header("Location: registration_form.php");
                    exit;
                } else {
                    // ❌ Invalid OTP
                    header("Location: ../login/otpscreen.php?email=$email&error=invalid_otp");
                    exit;
                }
            } else {
                // ❌ No user found
                header("Location: ../login/otpscreen.php?error=no_user");
                exit;
            }

            $stmt->close();
        } else {
            echo "Failed to prepare the SQL statement: " . $conn->error;
        }

        $conn->close();
    } else {
        echo "Required form fields are missing.";
    }
} else {
    echo "Invalid request method.";
}
