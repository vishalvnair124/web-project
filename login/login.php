<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Include the database connection
    include '../common/connection.php';

    // Check if the connection is valid
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if form data is set
    if (isset($_POST['email'], $_POST['password'])) {
        // Get the form data
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare the SQL query to fetch the user record
        $sql = "SELECT user_id, name, password FROM users WHERE email = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("s", $email);

            // Execute the statement
            $stmt->execute();

            // Store the result
            $stmt->store_result();

            // Check if a user exists with the given email
            if ($stmt->num_rows > 0) {
                // Bind result variables
                $stmt->bind_result($user_id, $name, $hashed_password);

                // Fetch the result
                $stmt->fetch();

                // Verify the password
                if (password_verify($password, $hashed_password)) {
                    // Start session and set session variables
                    session_start();
                    $_SESSION["user_id"] = $user_id;
                    $_SESSION["user"] = $name;
                    $_SESSION["isLogined"] = true;
                    $_SESSION["user_email"] = $email;

                    // Redirect with success message
                    header("Location: ../user/");
                    exit;
                } else {
                    // Redirect with invalid password message
                    header("Location: index.php?login=invalid_password");
                    exit;
                }
            } else {
                // Redirect with no user found message
                header("Location: index.php?login=no_user");
                exit;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Failed to prepare the SQL statement: " . $conn->error;
        }

        // Close the connection
        $conn->close();
    } else {
        echo "Required form fields are missing.";
    }
} else {
    echo "Invalid request method.";
}
