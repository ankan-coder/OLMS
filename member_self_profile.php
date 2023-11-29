<?php
session_start();
if (isset($_GET['uname'])) {
    $unme = $_GET['uname'];
}

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'member' && $_SESSION['musername'] == $unme)) {
    header("Location: index.html");
    exit();
}

include 'php_utils/_dbConnect.php';

$query_to_get_details_of_member = "SELECT * FROM `olms_members` WHERE `mem_uname` = ?";
$stmt2 = $conn->prepare($query_to_get_details_of_member);
$stmt2->bind_param("s", $unme);
$stmt2->execute();
$member_result = $stmt2->get_result();
$member_row = $member_result->fetch_assoc();
$stmt2->close();

if ($member_row > 0) {
    $type = "Member";
    $mem_name = $member_row['mem_nme'];
    $mem_phone = $member_row['mem_phn'];
    $mem_mail = $member_row['mem_mail'];
    $mem_addr = $member_row['mem_addr'];
    $mem_from = $member_row['reg_date'];
    $mem_pic = $member_row['img_path'];
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
            if (isset($mem_name)) {
                echo $mem_name;
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
            if ($type == "Member") {
                echo "<img src='" . $mem_pic . "' alt='profile_pic'>";
            }
            ?>
        </div>

        <div class="profile-details">
            <?php
            if ($type == "Member") {
                echo "<h2>Type: " . $type . "</h2>";
                echo "<h2>Name: " . $mem_name . "</h2>";
                echo "<h2>Phone: " . $mem_phone . "</h2>";
                echo "<h2>Email: " . $mem_mail . "</h2>";
                echo "<h2>Address: " . $mem_addr . "</h2>";
                echo "<h2>Registration date: " . $mem_from . "</h2>";
            }
            ?>
        </div>
    </div>
</body>

</html>