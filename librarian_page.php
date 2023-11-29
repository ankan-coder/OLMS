<?php
session_start();
if (!(isset($_SESSION['lloggedin']) && $_SESSION['lloggedin'] == true && isset($_SESSION['lusername']))) {
    header("location: librarian_login.php");
    exit();
}
$luname = $_SESSION['lusername'];
include 'php_utils/_dbConnect.php';

$query_to_get_details = "SELECT * FROM `olms_librarian` WHERE `lib_uname` = ?";
$stmt1 = $conn->prepare($query_to_get_details);
$stmt1->bind_param("s", $luname);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row_details = $result1->fetch_assoc();
$stmt1->close();

$name = $row_details['lib_nme'];
$timeout = 300; // 5 minutes in seconds
$current_time = time();
if (isset($_SESSION['last_activity']) && ($current_time - $_SESSION['last_activity'] > $timeout)) {
    // Log out the user
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session data
    header("Location: librarian_login.php"); // Redirect to the login page
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
    <title>Librarian Panel</title>
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
            <li><a href="members.php">Members</a></li>

            <li class="dropdown">
                <a href="#">Library</a>
                <div class="dropdown-content">
                    <a href="books.php">Books</a> <!-- Only to be viewed by the librarian and admin -->
                    <?php echo "<a href='issue_requests.php?uname=$luname'>Issue Requests</a>"; ?>
                    <a href="transactions.php">Transactions</a> <!-- Only to be viewed by the librarians and admin -->
                </div>
            </li>

            <li class="dropdown">
                <a href="#">Profile</a>
                <div class="dropdown-content">
                    <?php echo "<a href='librarian_self_profile.php?uname=$luname' target='_blank'>Profile</a>"; ?>
                    <a href="logout.php">Logout</a>
                </div>
            </li>
        </nav>
    </div>

    <div class="text">
        <h1>Hello <?php echo $name; ?><img src="CSS/Images/wave.png" alt="Hello">,</h1>
        <h2>Welcome to your library.</h2>
        <p>Please use the menu in the top right corner to navigate to different pages.</p>
    </div>
</body>

</html>