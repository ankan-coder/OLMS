<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'administrator')) {
    header("Location: index.html");
    exit();
}
include 'php_utils/_dbConnect.php';
$query_count = "SELECT COUNT(*) as row_count FROM olms_librarian";
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
    <title>Librarians</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/librarian_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>
    </div>

    <div class="librarian-list">
        <h1>List of Librarians</h1>
        <?php
        if ($rowCount == 0) {
            echo "
            <div class='no-librarian'>
                <p>No Librarian</p>
            </div>
        ";
        } else {
            $query = "SELECT * FROM `olms_librarian` ORDER BY `reg_date` DESC";
            $result = $conn->query($query);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <div class='librarian'>
                        <p>" . $row['lib_nme'] . "</p>
                        <a href='librarian_profile_from_admin.php?uname=" . $row['lib_uname'] . "'>Profile</a>
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