<html>
    <head><title>sendEmail</title></head>
    <body>
       
<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

 require 'phpmailer/src/Exception.php'; 
 require 'phpmailer/src/PHPMailer.php'; 
 require 'phpmailer/src/SMTP.php'; 

 session_start(); // Start the session

// Retrieve the data
$email = $_SESSION['email'];
$fullname = $_SESSION['fullname'];
$empid = $_SESSION['empid'];
$subject = $_SESSION['subject'];
$message = $_SESSION['message'];
$goback=$_SESSION['goback'];


// Unset the session variables
unset($_SESSION['email']);
unset($_SESSION['fullname']);
unset($_SESSION['empid']);
unset($_SESSION['subject']);
unset($_SESSION['message']);
unset($_SESSION['goback']);
sendEmail($email,$fullname, $subject,$message,$goback);



 function sendEmail($recipient,$name, $subject, $message,$goback){
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
                         //Enable verbose debug output
    $mail->isSMTP();                                    //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';               //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                           //Enable SMTP authentication
    $mail->Username   = 'database71231@gmail.com';      //Your Gmail Id
    $mail->Password   = 'emss jdao gbko canb';          //Your App password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //Enable implicit TLS encryption
    $mail->Port       = 587;  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('database71231@gmail.com', 'Admin');
    $mail->addAddress($recipient, $name);     //Add a recipient
  
    $mail->addReplyTo('database71231@gmail.com', 'Admin');
    

    //Attachments
   // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
   // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                     //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $message;
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
    // progress indicator

    echo 'Message has been senting';
    $mail->send();
    echo 'Message has been sent';
    header($goback);
} catch (Exception $e) {
    
}

}
?> 
    </body>
</html>