<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Drop4Life🩸</title>
    <link rel="stylesheet" href="./adminstyles/loginstyle.css" />
</head>

<body>
    <div class="wrapper">
        <div class="form-wrapper sign-in">
            <form action="login_check.php" method="post">
                <h2>Admin Login🩸</h2>

                <div class="input-group">
                    <input type="email" name="email" required />
                    <label for="email">Email</label>
                </div>
                <div class="input-group">
                    <input type="password" name="password" required />
                    <label for="password">Password</label>
                </div>
                <?php
                if (isset($_GET['error'])) {
                    echo '<p class="error-message">' . htmlspecialchars($_GET['error']) . '</p>';
                }
                ?>
                <button type="submit" class="btn">Login🩸</button>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>