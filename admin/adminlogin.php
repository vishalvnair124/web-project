<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Drop4Life🩸 - Admin Login</title>
    <link rel="stylesheet" href="./adminstyles/loginstyle.css" />
</head>

<body>
    <div class="wrapper">
        <div class="form-wrapper sign-in">
            <form action="login_check.php" method="post">
                <h2>Admin Login🩸</h2>


                <!-- CSRF Protection -->
                <?php
                session_start();
                $csrf_token = bin2hex(random_bytes(32));
                $_SESSION['csrf_token'] = $csrf_token;
                ?>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"
                <div class="input-group">
                    <input type="email" name="email" id="email" required autocomplete="email" />
                    <label for="email">Email</label>
                </div>
                <div class="input-group">
                    <input type="password" name="password" id="password" required autocomplete="current-password" />
                    <label for="password">Password</label>
                </div>

                <!-- Error Handling -->
                <?php
                if (isset($_GET['error'])) {
                    $error_message = htmlspecialchars($_GET['error']); 
                    echo '<p class="error-message">';
                    switch ($error_message) {
                        case 'invalid_credentials':
                            echo "Invalid email or password. Please try again.";
                            break;
                        case 'csrf_failed':
                            echo "Security verification failed. Please refresh and try again.";
                            break;
                        default:
                            echo "An error occurred. Please try again.";
                            break;
                    }
                    echo '</p>';
                }
                ?>

                <button type="submit" class="btn">Login🩸</button>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>
