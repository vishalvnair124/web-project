<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Drop4Life🩸</title>
  <link rel="stylesheet" href="newstyle.css" />
  <link rel="stylesheet" href="alertstyle.css">
</head>

<body>
  <div class="wrapper">
    <!-- Sign Up Form -->
    <div class="form-wrapper sign-up">
    <form action="signup.php" method="post">
  <h2>Sign Up🩸</h2>
  <div class="input-group">
    <input type="text" name="name" required />
    <label for="name">Name</label>
  </div>
  <div class="input-group">
    <input type="email" name="email" required />
    <label for="email">Email</label>
  </div>
  <button type="submit" class="btn">Get OTP 🩸</button>
  <div class="sign-link">
    


  </div>
</form>
    </div>

    <!-- Login Form -->
    <div class="form-wrapper sign-in">
      <form action="./login.php" method="post">
        <h2>Login🩸</h2>
        <div class="input-group">
          <input type="email" name="email" required />
          <label for="email">Email</label>
        </div>
        <div class="input-group">
          <input type="password" name="password" required />
          <label for="password">Password</label>
        </div>
        <div class="forgot-pass">
          <a href="#">Forgot Password?</a>
        </div>
        <button type="submit" class="btn">Login🩸</button>
        <div class="sign-link">
          <p>
            Don't have an account? <a href="#" class="signUp-link">Sign Up</a>
          </p>
        </div>
      </form>
    </div>
  </div>

  <!-- Info Box -->
  <?php if (isset($_GET['login']) || isset($_GET['signup'])) : ?>
    <div class="info <?php echo (isset($_GET['login']) && $_GET['login'] == 'success') || (isset($_GET['signup']) && $_GET['signup'] == 'success') ? 'success' : 'error'; ?>" id="infoBox">
      <div class="info__icon">
        <svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 1.5c-5.79844 0-10.5 4.70156-10.5 10.5 0 5.7984 4.70156 10.5 10.5 10.5 5.7984 0 10.5-4.7016 10.5-10.5 0-5.79844-4.7016-10.5-10.5-10.5zm.75 15.5625c0 .1031-.0844.1875-.1875.1875h-1.125c-.1031 0-.1875-.0844-.1875-.1875v-6.375c0-.1031.0844-.1875.1875-.1875h1.125c.1031 0 .1875.0844.1875.1875zm-.75-8.0625c-.2944-.00601-.5747-.12718-.7808-.3375-.206-.21032-.3215-.49305-.3215-.7875s.1155-.57718.3215-.7875c.2061-.21032.4864-.33149.7808-.3375.2944.00601.5747.12718.7808.3375.206.21032.3215.49305.3215.7875s-.1155.57718-.3215.7875c-.2061.21032-.4864.33149-.7808.3375z" fill="#393a37"></path>
        </svg>
      </div>
      <div class="info__title">
        <?php
        if (isset($_GET['login'])) {
          if ($_GET['login'] == 'success') {
            echo "Login successful!";
          } elseif ($_GET['login'] == 'invalid_password') {
            echo "Invalid password.";
          } elseif ($_GET['login'] == 'no_user') {
            echo "No user found with this email.";
          }
        } elseif (isset($_GET['signup'])) {
          if ($_GET['signup'] == 'success') {
            echo "Sign Up successful!";
          } elseif ($_GET['signup'] == 'error') {
            echo "Sign Up failed. Please try again.";
          } elseif ($_GET['signup'] == 'email_exists') {
            echo "Email already exists. Please use a different email.";
          }
        }
        ?>
      </div>
      <div class="info__close" onclick="closeInfoBox()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" viewBox="0 0 20 20" height="20">
          <path fill="#393a37" d="M15.8333 5.34166l-1.175-1.175-4.6583 4.65834-4.65833-4.65834-1.175 1.175 4.65833 4.65834-4.65833 4.6583 1.175 1.175 4.65833-4.6583 4.6583 4.6583 1.175-1.175-4.6583-4.6583z"></path>
        </svg>
      </div>
    </div>

    <script>
      function closeInfoBox() {
        var infoBox = document.getElementById('infoBox');
        infoBox.style.opacity = '0';
        setTimeout(function() {
          infoBox.style.display = 'none';
        }, 500); // Match the transition time
      }

      window.onload = function() {
        var infoBox = document.getElementById('infoBox');
        if (infoBox) {
          infoBox.style.opacity = '1';
          setTimeout(closeInfoBox, 2000); // Show for 2 seconds
        }
      };
    </script>
  <?php endif; ?>

  <script src="script.js"></script>
</body>

</html>