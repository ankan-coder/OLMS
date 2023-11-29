<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';

$_SESSION['form'] = 1;

// Assume you have your form processing logic here
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    include 'php_utils/_dbConnect.php';

    // Check for database connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check for POST variables
    if (isset($_POST['r-email'], $_POST['type'])) {
        $email = $_POST['r-email'];
        $_SESSION['email'] = $email;
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

        if ($type === 'librarian') {
            $qry2 = "SELECT * FROM `olms_librarian` WHERE `lib_mail` = ?";
        } else if ($type === 'member') {
            $qry2 = "SELECT * FROM `olms_members` WHERE `mem_mail` = ?";
        }

        // Use prepared statement to avoid SQL injection
        $stmt = mysqli_prepare($conn, $qry2);

        if (!$stmt) {
            die("Error preparing statement: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $res2 = mysqli_stmt_get_result($stmt);
        $rc = mysqli_num_rows($res2);

        $_SESSION['t'] = ($type === 'librarian') ? 'l' : 'm';

        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;

        if ($rc === 1) {
            $_SESSION['temp'] = $email;
            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP(); //Send using SMTP
                $mail->Host       = 'smtp.gmail.com'; //Set the SMTP server to send through
                $mail->SMTPAuth   = true; //Enable SMTP authentication
                $mail->Username   = 'mailankandgp@gmail.com'; //SMTP username
                $mail->Password   = 'xkwophkjlgljzxcb'; //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //Enable implicit TLS encryption
                $mail->Port       = 587; //TCP port to connect to; use 465 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_SMTPS`

                //Recipients
                $mail->setFrom('mailankandgp@gmail.com', 'Password recovery - OLMS');
                $mail->addAddress($email);     //Add a recipient

                //Content
                $mail->isHTML(true); //Set email format to HTML
                $mail->Subject = 'Password Recovery - OLMS';
                $mail->Body    = "The OTP for recovery of the OLMS login is " . $_SESSION['otp'];

                $mail->send();
                // echo "Two day's reminder has been sent";
            } catch (Exception $e) {
                echo "The otp could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $notfound = "Email not found!";
        }
        $_SESSION['form'] = 2;
    } else if (isset($_POST['checkotp'])) {
        if ($_POST['r-OTP'] === $_SESSION['otp']) {
            $_SESSION['form'] = 3;
        } else {
            $wrongotp = "Wrong OTP entered!";
        }
    } else if (isset($_POST['changepwd'])) {
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot password</title>
    <link rel="stylesheet" href="CSS/forgotpass_nav_style.css">
    <link rel="stylesheet" href="CSS/forgot-password.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>
    </div>

    <div class="container">
        <?php
        // Check for session variables
        if (isset($_SESSION['form']) && isset($_SESSION['otp']) && isset($_SESSION['email']) && isset($_SESSION['t'])) {
            if ($_SESSION['form'] === 1) {
                echo '
                    <div class="form" id="f1">
                    <form action="forgot-password.php" method="post" enctype="multipart/form-data">
                        <div class="heading">
                            <h2>Recover Password</h2>
                        </div>

                        <div class="inner-form">
                            <label for="email">Email:</label>
                            <input type="text" name="r-email" placeholder="Enter your email">
                            <label for="email">Which type of user are you?</label>
                            <select name="type">
                                <option value="">---Select the type---</option>
                                <option value="librarian">Librarian</option>
                                <option value="member">Member</option>
                            </select>

                            <button type="submit" name="sendotp">Send OTP</button>
                        </div>
                    </form>
                </div>
                    ';
            } else if ($_SESSION['form'] === 2) {
                echo '
                    <div class="form" id="f2">
                    <form action="forgot-password.php" method="post" enctype="multipart/form-data">
                        <div class="heading">
                            <h2>Recover Password</h2>
                        </div>

                        <div class="inner-form">
                            <label for="email">OTP:</label>
                            <input type="text" name="r-OTP" placeholder="Enter the OTP">

                            <button type="submit" name="checkotp">Send OTP</button>
                        </div>
                    </form>
                </div>
                    ';
            } else if ($_SESSION['form'] === 3) {
                echo '
                <div class="form" id="f2">
                <form action="forgot-password.php" method="post" enctype="multipart/form-data">
                    <div class="heading">
                        <h2>Change Password</h2>
                    </div>

                    <div class="inner-form">
                        <label for="pass">Enter new password: </label>
                        <input type="text" name="pwd" placeholder="Enter new password">
                        <label for="cnf-pass">Confirm new password: </label>
                        <input type="password" name="pwd" placeholder="Confirm new password">

                        <button type="submit" name="changepwd">Change password</button>
                    </div>
                </form>
            </div>
                ';
            }
        }
        ?>
    </div>
</body>

</html>