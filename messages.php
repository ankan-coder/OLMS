<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'administrator')) {
    header("Location: index.html");
    exit();
}
include 'php_utils/_dbConnect.php';
$query_count = "SELECT COUNT(*) as row_count FROM olms_messages";
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
    <title>Messages</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/message_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>
    </div>

    <div class="message-box">
        <h1>Message List</h1>
        <?php
        if ($rowCount == 0) {
            echo "
            <div class='no-message'>
                <p>No messages</p>
            </div>
        ";
        } else {
            $query = "SELECT * FROM `olms_messages` ORDER BY `msg_time` DESC";
            $result = $conn->query($query);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                <div class='message'>
                    <p>Sender Name: " . $row['msg_nme'] . "</p>
                    <p>Sender Email: " . $row['msg_mail'] . "</p>
                    <p>Message: " . $row['msg_message'] . "</p>
                    <p>Time: " . $row['msg_time'] . "</p>
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