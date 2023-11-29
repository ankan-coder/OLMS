<?php
session_start();

if (!(isset($_SESSION['aloggedin']) && $_SESSION['aloggedin'] == true && isset($_SESSION['ausername']) && $_SESSION['ausername'] == 'admin')) {
    header("location: admin_login.php");
    exit();
}

// Set the timeout period to 5 minutes (300 seconds)
$timeout = 300; // 5 minutes in seconds

// Get the current time
$current_time = time();

// Check if the session variable for last activity time is set
if (isset($_SESSION['last_activity']) && ($current_time - $_SESSION['last_activity'] > $timeout)) {
    // Log out the user
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session data
    header("Location: admin_login.php"); // Redirect to the login page
    exit(); // Stop executing the script
}

// Update the last activity time in the session variable
$_SESSION['last_activity'] = $current_time;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="CSS/internal_nav_style.css">
    <link rel="stylesheet" href="CSS/page_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">

    <script>

    </script>
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>

        <nav>
            <li class="dropdown">
                <a href="#">Users</a>
                <div class="dropdown-content">
                    <a href="librarian_signup.php">Add Librarian</a>
                    <a href="librarian.php">Librarians</a>
                    <a href="members.php">Members</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">Library</a>
                <div class="dropdown-content">
                    <a href="books.php">Books</a>
                    <a href="auto_return.php">Process today's returns</a>
                    <a href="transactions.php">Transactions</a>
                </div>
            </li>

            <li><a href="messages.php">Messages</a></li>
            <li><a href="logout.php">Logout</a></li>
        </nav>
    </div>

    <div class="text" id="text">
        <h1>Hello Administrator<img src="CSS/Images/wave.png" alt="Hello">,</h1>
        <h2>Welcome to your control panel.</h2>
        <p>Please use the menu in the top right corner to navigate to different pages.</p>
    </div>

    <div class="message" id="messages">

    </div>
</body>

</html>