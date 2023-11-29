<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'administrator' && $_SESSION['user_role'] !== 'librarian')) {
    header("Location: index.html");
    exit();
}
include 'php_utils/_dbConnect.php';
$query_count = "SELECT COUNT(*) as row_count FROM olms_members";
$result_count = $conn->query($query_count);
if ($result_count) {
    $row = $result_count->fetch_assoc();
    $rowCount = $row['row_count'];
    $result_count->free();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/member_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>
    </div>

    <div class="member-list">
        <h1>List of Members</h1>
        <?php
        if ($rowCount == 0) {
            echo "
            <div class='no-member'>
                <p>No Members</p>
            </div>
        ";
        } else {
            $query = "SELECT * FROM `olms_members` ORDER BY `reg_date` DESC";
            $result = $conn->query($query);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <div class='member'>
                        <p>" . $row['mem_nme'] . "</p>
                        <a href='member_profile_from_admin_and_librarian.php?uname=" . $row['mem_uname'] . "'>Profile</a>
                    </div>
                ";
                }
            }
        }
        ?>
    </div>

</body>

</html>
<?php
$conn->close();
?>