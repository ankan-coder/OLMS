<?php
session_start();
if (isset($_GET['uname'])) {
    $unme = $_GET['uname'];
}
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'librarian' && $_SESSION['lusername'] !== $unme)) {
    header("Location: index.html");
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
    header("Location: librarian_login.php"); // Redirect to the login page
    exit(); // Stop executing the script
}

// Update the last activity time in the session variable
$_SESSION['last_activity'] = $current_time;

include 'php_utils/_dbConnect.php';

$query_count = "SELECT COUNT(*) as row_count FROM olms_issue_requests";
$result_count = $conn->query($query_count);
if ($result_count) {
    $row = $result_count->fetch_assoc();
    $rowCount = $row['row_count'];
    $result_count->free();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['issued_book_id'])) {

    //Get number of rows in transaction table
    $trans_query_count = "SELECT COUNT(*) as trans_row_count FROM olms_transactions";
    $trans_result_count = $conn->query($trans_query_count);
    if ($trans_result_count) {
        $trans_row = $trans_result_count->fetch_assoc();
        $transrowCount = $trans_row['trans_row_count'];
        $trans_result_count->free();
    }

    //Get rows from issue requests table
    $query_issue_requests = "SELECT * FROM `olms_issue_requests`";
    $result_issue_requests = $conn->query($query_issue_requests);
    if ($result_issue_requests) {
        $requests_row = $result_issue_requests->fetch_assoc();
        $result_issue_requests->free();
    }

    //Get rows from members table where member username is equal to the requested by username
    $query_members = "SELECT `mem_id`, `mem_nme` FROM `olms_members` WHERE `mem_uname` = ?";
    $stmt_mem = $conn->prepare($query_members);
    $stmt_mem->bind_param("s", $requests_row['requested_by_uname']);
    $stmt_mem->execute();
    $stmt_mem->bind_result($requested_by_id, $requested_by_name); // Adjust the number of variables here
    $stmt_mem->fetch();
    $stmt_mem->close();

    //Get rows from librarian table where librarian username is equal to the loggedin username
    $query_library = "SELECT * FROM `olms_librarian` WHERE `lib_uname` = ?";
    $stmt_lib = $conn->prepare($query_library);
    $stmt_lib->bind_param("s", $unme);
    $stmt_lib->execute();
    $result_librarian = $stmt_lib->get_result();
    $row_librarian = $result_librarian->fetch_assoc();
    $stmt_lib->close();

    $Date = date('d-m-Y');

    $book_no = $requests_row['requested_book_id'];
    $book_name = $requests_row['requested_book_nme'];
    $name = $requests_row['requested_by'];
    $issued_by = $_POST['issued_by'];
    $issued_on = $Date;
    $return_date = date('d-m-Y', strtotime($Date . ' + 10 days'));
    $issued_by_id = $row_librarian['lib_id'];
    $issued_by_name = $row_librarian['lib_nme'];
    $request_id = $requests_row['request_id'];


    $transaction_id = 'T' . ($transrowCount + 1);
    $transaction_type = "Issue";

    if ($book_no != '' && $name != '' && $issued_by != '' && $issued_on != '' && $return_date != '' && $transaction_id != '' && $transaction_type != '' && $book_name != '' && $requested_by_id != '' && $requested_by_name != '' && $issued_by_id != '' && $issued_by_name != '' && $request_id != '') {
        require 'php_utils/_dbConnect.php';
        $issue_sql_query = "INSERT INTO `olms_issued`(`issued_book_no`, `issued_to`, `issued_by`, `issued_on`, `issued_upto`) VALUES (?, ?, ?, ?, ?)";
        $stmt_issue = $conn->prepare($issue_sql_query);
        $stmt_issue->bind_param("sssss", $book_no, $name, $issued_by, $issued_on, $return_date);

        $transaction_sql_query = "INSERT INTO `olms_transactions`(`transaction_id`, `transaction_type`, `transaction_date`, `book_id`, `book_name`, `member_id`, `member_name`, `librarian_id`, `librarian_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_transaction = $conn->prepare($transaction_sql_query);
        $stmt_transaction->bind_param("sssssssss", $transaction_id, $transaction_type, $issued_on, $book_no, $book_name, $requested_by_id, $requested_by_name, $issued_by_id, $issued_by_name);

        $delete_query = "DELETE FROM `olms_issue_requests` WHERE `request_id` = ?";
        $stmt_dlt = $conn->prepare($delete_query);
        $stmt_dlt->bind_param("s", $request_id);

        if ($stmt_issue->execute()) {
            $stmt_issue->close();
        } else {
            $records_error = "Error: " . $stmt_issue->error;
        }

        if ($stmt_transaction->execute()) {
            $stmt_transaction->close();
        } else {
            $records_error = "Error: " . $stmt_transaction->error;
        }

        if ($stmt_dlt->execute()) {
            $stmt_dlt->close();
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Requested Books</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/issue_book_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
    <script>
        window.onload = function() {
            window.history.pushState({}, "");
        };
        window.addEventListener('popstate', function(event) {
            var referrer = document.referrer;
            if (referrer.includes("member_page.php") && "<?php echo $_SESSION['user_role']; ?>" === "member") {
                window.location.href = "member_page.php";
            }
        });
    </script>
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="librarian_page.php">OLMS</a>
        </div>
    </div>


    <div class="book-rack">
        <?php
        if ($rowCount === 0) {
            echo "
            <div class='no-book'>
                <p>No requests available</p>
            </div>
        ";
        } else {
            $query = "SELECT * FROM `olms_issue_requests` ORDER BY `requested_on` ASC"; // Assuming 'id' is your primary key
            $result = $conn->query($query);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                <div class='book'>
                    <p>Requested by: " . $row['requested_by'] . "</p>
                    <p>Book ID: " . $row['requested_book_id'] . "</p>
                    <p>Book name: " . $row['requested_book_nme'] . "</p>
                    <p>Requested Date: " . $row['requested_on'] . "</p>

                    <div class='buttons'>
                        <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                        <input type='hidden' name='issued_by' value='" . $unme . "'>
                        <input type='hidden' name='issued_book_id' value='" . $row['requested_book_id'] . "'>
                            <button type='submit' class='request-btn'>Issue</button>
                        </form>
                    </div>
                </div>
            ";
                }
            }
        }
        ?>
    </div>
    <div class="alert">
        <?php
        // Check for alerts from the previous page
        if (isset($records_error)) {
            echo "<h3>" . $records_error . "</h3>";
        }
        ?>
    </div>
</body>

</html>
<?php
$conn->close();
?>