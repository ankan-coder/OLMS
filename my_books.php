<?php
session_start();

//Getting the username from the page url
if (isset($_GET['uname'])) {
    $unme = $_GET['uname']; //Contains the username of the member (Declared globally)  -- 1
}

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'member')) {
    header("Location: member_login.php");
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
    header("Location: member_login.php"); // Redirect to the login page
    exit(); // Stop executing the script
}

// Update the last activity time in the session variable
$_SESSION['last_activity'] = $current_time;

include 'php_utils/_dbConnect.php';

//Fetching all the rows from the members table where mem_nme is the username of the loggedin member
$query_member_name = "SELECT * FROM `olms_members` WHERE `mem_uname` = '" . $unme . "'";
$result_member_name = $conn->query($query_member_name);
if ($result_member_name) {
    $row_name = $result_member_name->fetch_assoc(); //Stores row from olms_members
    $result_member_name->free();
}

$query_count = "SELECT * FROM olms_issued WHERE `issued_to` = '" . $row_name['mem_nme'] . "'";
$result_count = $conn->query($query_count);
$num_rows = ($result_count) ? $result_count->num_rows : 0;
if ($result_count) {
    $row = $result_count->fetch_assoc();
    $result_count->free();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $ibn = $_POST['returning_book_id'];

    // Get number of rows in transaction table in order to use the count to initialize the variable $transaction_id
    $trans_query_count = "SELECT COUNT(*) as trans_row_count FROM olms_transactions";
    $trans_result_count = $conn->query($trans_query_count);
    if ($trans_result_count) {
        $trans_row = $trans_result_count->fetch_assoc();
        $transrowCount = $trans_row['trans_row_count'];
        $trans_result_count->free();
    }

    $qry = "SELECT * FROM olms_issued WHERE `issued_book_no` = '" . $ibn . "'";
    $rs1 = $conn->query($qry);
    if ($rs1) {
        $rw1 = $rs1->fetch_assoc();
        $rs1->free();
    }

    $mem_id_qry = "SELECT * FROM `olms_members` WHERE `mem_nme` = '" . $rw1['issued_to'] . "'";
    $mem_res = mysqli_query($conn, $mem_id_qry);
    $memberRow = mysqli_fetch_array($mem_res);
    $memberId = $memberRow['mem_id'];

    $transaction_id = 'T' . ($transrowCount + 1);
    $tran_type = "Return";
    $today = new DateTime();

    $book_id_qry = "SELECT * FROM `olms_books` WHERE `book_id` = '" . $ibn . "'";
    $book_res = mysqli_query($conn, $book_id_qry);
    $bookRow = mysqli_fetch_array($book_res);
    $bookName = $bookRow['book_nme'];


    $lib_id_qry = "SELECT * FROM `olms_librarian` WHERE `lib_uname` = '" . $rw1['issued_by'] . "'";
    $lib_res = mysqli_query($conn, $lib_id_qry);

    // Check if the query was successful and returned rows
    if ($lib_res && mysqli_num_rows($lib_res) > 0) {
        $librarianRow = mysqli_fetch_array($lib_res);
        $librarianId = $librarianRow['lib_id'];
        $librarianName = $librarianRow['lib_nme'];
    }

    $isto = $rw1['issued_to'];
    $tdte = $today->format('d-m-Y');

    $transaction_sql_query = "INSERT INTO `olms_transactions`(`transaction_id`, `transaction_type`, `transaction_date`, `book_id`, `book_name`, `member_id`, `member_name`, `librarian_id`, `librarian_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_transaction = $conn->prepare($transaction_sql_query);
    $stmt_transaction->bind_param("sssssssss", $transaction_id, $tran_type, $tdte, $ibn, $bookName, $memberId, $isto, $librarianId, $librarianName);
    $stmt_transaction->execute();  // -------1

    $delete_query = "DELETE FROM `olms_issued` WHERE `issued_book_no` = ? AND `issued_to` = ?";
    $stmt_dlt = $conn->prepare($delete_query);
    $stmt_dlt->bind_param("ss", $rw1['issued_book_no'], $rw1['issued_to']);
    $stmt_dlt->execute(); // ------------- 2
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarians</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/my_books_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="member_page.php">OLMS</a>
        </div>
    </div>

    <h1 class="heading">List of books in your stack</h1>
    <div class="mybooks">
        <?php
        if ($num_rows === 0) {
            echo "
            <div class='no-books-issued'>
                <p>No books issued in your stack</p>
            </div>
        ";
        } else {
            $qr = "SELECT * FROM olms_issued WHERE `issued_to` = '" . $row_name['mem_nme'] . "'";
            $rs = $conn->query($qr);
            if ($rs) {
                while ($rw = $rs->fetch_assoc()) {
                    $book_qr = "SELECT * FROM `olms_books` WHERE `book_id` = '" . $rw['issued_book_no'] . "'";
                    $book_rs = $conn->query($book_qr);
                    if ($book_rs) {
                        $bk = $book_rs->fetch_assoc();
                        $book_rs->free();
                    }

                    $bookname = $bk['book_nme'];
                    $author = $bk['book_auth'];
                    $publication = $bk['book_pub'];
                    $genre = $bk['book_genre'];

                    echo "
                        <div class='book'>
                        <p>Title: " . $bookname . "</p>
                        <p>Author: " . $author . "</p>
                        <p>Publication: " . $publication . "</p>
                        <p>Genre: " . $genre . "</p>

                        <div class='buttons'>
                            <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                <input type='hidden' name='returning_book_id' value='" . $rw['issued_book_no'] . "'>
                                <button type='submit' class='return-btn'>Return</button>
                            </form>
                        </div>
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