<?php

date_default_timezone_set("Asia/Kolkata");
$msgtime = date('d-m-Y h:i:sa');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $_POST["name"];
    $email = $_POST["mail"];
    $msg = $_POST["msg"];
    $msg_time = $_POST["time"];

    if ($name != '' && $email != '' && $msg != '' && $msg_time != '') {
        require 'php_utils/_dbConnect.php';

        $table_insert_sql_query = "INSERT INTO `olms_messages`(`msg_nme`, `msg_mail`, `msg_message`, `msg_time`) VALUES ('$name', '$email', '$msg', '$msg_time')";

        if ($conn->query($table_insert_sql_query)) {
            $records_success = "Message sent successfully!";
        } else {
            $records_error = "Error: " . $conn->error;
        }
    } else {
        $blank_entries = "Cannot insert blank entries";
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/contact_us_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="index.html">OLMS</a>
        </div>

        <nav>
            <li><a href="about.html">About us</a></li>

            <li><a href="member_signup.php">Get Membership</a></li>
            <li class="dropdown">
                <a href="#">Login</a>
                <div class="dropdown-content">
                    <a href="admin_login.php">Administrator</a>
                    <a href="librarian_login.php">Librarian</a>
                    <a href="member_login.php">Member</a>
                </div>
            </li>
        </nav>
    </div>

    <div class="form">
        <form action="contact.php" method="post" enctype="multipart/form-data">
            <div class="heading">
                <h2>Contact Us</h2>
            </div>

            <div class="inner-form">
                <input type="hidden" name="time" value="<?php echo $msgtime; ?>">
                <label for="name">Name:</label>
                <input type="text" name="name" placeholder="Enter your name">
                <label for="mail">Email:</label>
                <input type="email" name="mail" placeholder="Enter your e-mail">
                <label for="message">Message:</label>
                <input type="text" name="msg" placeholder="Enter your message">

                <button type="submit">Send message</button>

                <?php
                if (isset($records_success)) {
                    echo '<button disabled>' . $records_success . '</button>';
                } else if (isset($records_error)) {
                    echo '<button disabled>' . $records_error . '</button>';
                } else if (isset($blank_entries)) {
                    echo '<button disabled>' . $blank_entries . '</button>';
                }
                ?>
            </div>
        </form>
    </div>
</body>

</html>