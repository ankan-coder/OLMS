<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'administrator')) {
    header("Location: index.html");
    exit();
}
if (isset($_GET['uname'])) {
    $unme = $_GET['uname'];
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

include 'php_utils/_dbConnect.php';

$query_to_get_details_of_librarian = "SELECT * FROM `olms_librarian` WHERE `lib_uname` = ?";
$stmt2 = $conn->prepare($query_to_get_details_of_librarian);
$stmt2->bind_param("s", $unme);
$stmt2->execute();
$librarian_result = $stmt2->get_result();
$librarian_row = $librarian_result->fetch_assoc();
$stmt2->close();

if ($librarian_row > 0) {
    $type = "Librarian";
    $lib_name = $librarian_row['lib_nme'];
    $lib_phone = $librarian_row['lib_phn'];
    $lib_mail = $librarian_row['lib_mail'];
    $lib_addr = $librarian_row['lib_addr'];
    $lib_from = $librarian_row['reg_date'];
    $lib_pic = $librarian_row['img_path'];
} else {
    echo "Username not valid!";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/profile_nav_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
    <title><?php
            if (isset($lib_name)) {
                echo $lib_name;
            }
            ?></title>
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>
    </div>

    <div class="profile">
        <div class="profile-picture">
            <?php
            if ($type == "Librarian") {
                echo "<img src='" . $lib_pic . "' alt='profile_pic'>";
            }
            ?>
        </div>

        <div class="profile-details">
            <?php
            if ($type == "Librarian") {
                echo "<h2>Type: " . $type . "</h2>";
                echo "<h2>Name: " . $lib_name . "</h2>";
                echo "<h2>Phone: " . $lib_phone . "</h2>";
                echo "<h2>Email: " . $lib_mail . "</h2>";
                echo "<h2>Address: " . $lib_addr . "</h2>";
                echo "<h2>Registration date: " . $lib_from . "</h2>";
            }
            ?>
        </div>
    </div>
</body>

</html>