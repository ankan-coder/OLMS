<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Checking if the username and password are correct or not
    if ($username === 'admin' && $password === 'admin') {
        session_start(); // Start the session
        $_SESSION['aloggedin'] = true; // Setting loggedin variable as true
        $_SESSION['ausername'] = 'admin'; // Storing the session username to a variable ausername(Here a is for admin)
        $_SESSION['user_role'] = 'administrator'; // Setting a new session variable called user_role to check for which type of user has logged in
        header("Location: admin_page.php"); // On successful login the page gets redirected to admin_page.php
        exit;
    } else {
        $success_status = "Invalid username or password"; // Saving the error message to tha variable $session_status
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/login_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="index.html">OLMS</a>
        </div>

        <nav>
            <li><a href="about.html">About us</a></li>

            <li> <a href="member_signup.php">Get membership</a></li>
            
            <li class="dropdown">
                <a href="#">Login</a>
                <div class="dropdown-content">
                    <a href="librarian_login.php">Librarian</a>
                    <a href="member_login.php">Member</a>
                </div>
            </li>

            <li><a href="contact.php">Contact us</a></li>
        </nav>
    </div>

    <div class="form">
        <form action="admin_login.php" method="post">
            <div class="heading">
                <h2>Admin login</h2>
            </div>

            <div class="inner-form">
                <label for="username">Username:</label>
                <input type="text" name="username" placeholder="Enter the username">
                <label for="password">Password:</label>
                <input type="password" name="password" placeholder="Enter the password">

                <button type="submit">Sign In</button>

                <?php
                if (isset($success_status)) {
                    echo '<button disabled>' . $success_status . '</button>';
                }
                ?>
            </div>
        </form>
    </div>
</body>

</html>