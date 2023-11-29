<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'administrator' && $_SESSION['user_role'] !== 'librarian')) {
    header("Location: index.html");
    exit();
}
include 'php_utils/_dbConnect.php';

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
$_SESSION['last_activity'] = $current_time;

$query_count = "SELECT COUNT(*) as row_count FROM olms_transactions";
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
    <title>Transactions</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/trans_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>
    </div>

    <h1 class="heading">List of Transactions</h1>
    <div class="transaction-list">
        <?php
        if ($rowCount == 0) {
            echo "
            <div class='no-transactions'>
                <p>No Transactions</p>
            </div>
        ";
        } else {
            $query = "SELECT * FROM `olms_transactions` ORDER BY `transaction_no` DESC";
            $result = $conn->query($query);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    if ($row['transaction_type'] === 'Issue') {
                        echo "
                    <div class='transaction'>
                        <p>Transaction ID: " . $row['transaction_id'] . "</p>
                        <p>Transaction Type: " . $row['transaction_type'] . "</p>
                        <p>Transaction date: " . $row['transaction_date'] . "</p>
                        <p>Book ID: " . $row['book_id'] . "</p>
                        <p>Book name: " . $row['book_name'] . "</p>
                        <p>Member ID: " . $row['member_id'] . "</p>
                        <p>Member name: " . $row['member_name'] . "</p>
                        <p>Librarian ID: " . $row['librarian_id'] . "</p>
                        <p>Librarian name: " . $row['librarian_name'] . "</p>
                    </div>
                ";
                    } else if ($row['transaction_type'] === 'Return') {
                        echo "
                    <div class='transaction'>
                        <p>Transaction ID: " . $row['transaction_id'] . "</p>
                        <p>Transaction Type: " . $row['transaction_type'] . "</p>
                        <p>Transaction date: " . $row['transaction_date'] . "</p>
                        <p>Book ID: " . $row['book_id'] . "</p>
                        <p>Book name: " . $row['book_name'] . "</p>
                        <p>Member ID: " . $row['member_id'] . "</p>
                        <p>Member name: " . $row['member_name'] . "</p>
                    </div>
                ";
                    }
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