<?php

session_start();

if (!(isset($_SESSION['mloggedin']) && $_SESSION['mloggedin'] == true && isset($_SESSION['musername']) && $_SESSION['musername'] != 'admin')) {
    header("location: member_login.php");
    exit();
}

$muname = $_SESSION['musername'];
include 'php_utils/_dbConnect.php';

$query_to_get_details = "SELECT * FROM `olms_members` WHERE `mem_uname` = ?";
$stmt1 = $conn->prepare($query_to_get_details);
$stmt1->bind_param("s", $muname);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row_details = $result1->fetch_assoc();
$stmt1->close();

$name = $row_details['mem_nme'];


// Set the timeout period to 5 minutes (300 seconds)
$timeout = 300; // 5 minutes in seconds

// Get the current time
$current_time = time();

// Check if the session variable for last activity time is set
if (isset($_SESSION['last_activity']) && ($current_time - $_SESSION['last_activity'] > $timeout)) {
    // Log out the user
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session data
    header("Location: member_login.php"); // Redirect to the login page
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
    <title>Member Panel</title>
    <link rel="stylesheet" href="CSS/internal_nav_style.css">
    <link rel="stylesheet" href="CSS/page_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>

        <nav>
            <li class="dropdown">
                <a href="#">Library</a>
                <div class="dropdown-content">
                    <?php echo "<a href='member_issue_books.php?uname=$muname'>Request Books</a>"; ?>
                    <?php echo "<a href='my_books.php?uname=$muname'>My Books</a>"; ?>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">Profile</a>
                <div class="dropdown-content">
                    <?php echo "<a href='member_self_profile.php?uname=$muname' target='_blank'>Profile</a>"; ?>
                    <a href="logout.php">Logout</a>
                </div>
            </li>
        </nav>
    </div>

    <div class="text">
        <h1>Hello <?php echo $name; ?><img src="CSS/Images/wave.png" alt="Hello">,</h1>
        <h2>Welcome to the library.</h2>
        <p>Please use the menu in the top right corner to navigate to different pages.</p>
    </div>
</body>

</html>