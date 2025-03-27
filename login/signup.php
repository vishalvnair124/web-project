<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Include the database connection
    include '../common/connection.php';

    // Check if the connection is valid
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if form data is set
    if (isset($_POST['name'], $_POST['email'])) {
        // Get the form data
        $name = $_POST['name'];
        $email = $_POST['email'];


        // Check if email already exists
        $checkEmailSql = "SELECT email FROM users WHERE email = ?";
        if ($stmt = $conn->prepare($checkEmailSql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Email already exists
                header("Location: index.php?signup=email_exists");
            } else {

                //otp email 
                //email
                //session
                //redirect

                // vyshanavi
                // vyshnavycm@gmail.com
                // sub
                // otp
                //redirect



                // Hash the password
                // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                //create  new user
                // status 7

                // mail sent



                // // Hash the password
                // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // // Prepare the SQL query to insert the user record
                // $sql = "INSERT INTO users (user_name, email, user_password) VALUES (?, ?, ?)";
                // if ($stmt = $conn->prepare($sql)) {
                //     $stmt->bind_param("sss", $name, $email, $hashed_password);
                //     if ($stmt->execute()) {
                //         // Redirect with success message
                //         header("Location: index.php?signup=success");
                //     } else {
                //         // Redirect with error message
                //         header("Location: index.php?signup=error");
                //     }
                //     $stmt->close();
                // } else {
                //     echo "Failed to prepare the SQL statement: " . $conn->error;
                // }
            }
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
