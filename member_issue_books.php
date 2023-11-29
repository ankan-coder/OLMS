<?php

session_start(); //Starting session for the page

//Getting the username from the page url
if (isset($_GET['uname'])) {
    $unme = $_GET['uname']; //Contains the username of the member (Declared globally)  -- 1
}

//If the user is not a member then redirect the page to index.html
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'member' && $_SESSION['musername'] !== $unme)) {
    header("Location: index.html");
    exit();
}

//Page timeout on not using for 5 minutes
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

//Include database connect page
include 'php_utils/_dbConnect.php';

//Checking the row count of the books table
$query_count = "SELECT COUNT(*) as row_count FROM olms_books";
$result_count = $conn->query($query_count);
if ($result_count) {
    $row = $result_count->fetch_assoc();
    $rowCount = $row['row_count'];   //Contains the number of books present (Declared globally)  -- 2
    $result_count->free();
}

//Form action starts
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['requested_book_id'])) {

    //Fetching all the rows in the books table where book_id is the requested book's id
    $query_book_name = "SELECT * FROM `olms_books` WHERE `book_id` = '" . $_POST['requested_book_id'] . "'";
    $result_book_name = $conn->query($query_book_name);
    if ($result_book_name) {
        $row_bookname = $result_book_name->fetch_assoc(); //Stores row from olms_books
        $result_book_name->free();
    }

    //Fetching all the rows from the members table where mem_nme is the username of the loggedin member
    $query_member_name = "SELECT * FROM `olms_members` WHERE `mem_uname` = '" . $unme . "'";
    $result_member_name = $conn->query($query_member_name);
    if ($result_member_name) {
        $row_name = $result_member_name->fetch_assoc(); //Stores row from olms_members
        $result_member_name->free();
    }

    //Fetching the count of the rows from issue requests table
    $request_count = "SELECT COUNT(*) as request_count FROM olms_issue_requests";
    $result_request = $conn->query($request_count);
    if ($result_request) {
        $row = $result_request->fetch_assoc();
        $requestCount = $row['request_count']; //Stores number of issue requests in the issue requests table 
        $result_request->free();
    }

    $date = date('d-m-Y'); //Fetching current date in a variable
    $request_id = 'R' . ($requestCount + 1) . '-' . ($date); //Generating a request ID for inserting in the table upon submitting

    /*If the following variables are not empty then insert them into the table issue requests table
        1. $row_name['mem_nme']
        2. $_POST['requested_book_id']
        3. $row_bookname['book_nme']
        4. $date
    */
    if ($row_name['mem_nme'] != '' && $_POST['requested_book_id'] != '' && $row_bookname['book_nme'] != '' && $date != '') {
        $request_sql_query = "INSERT INTO `olms_issue_requests`(`request_id`, `requested_by`, `requested_book_id`, `requested_book_nme`, `requested_on`, `requested_by_uname`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($request_sql_query);
        $stmt->bind_param("ssssss", $request_id, $row_name['mem_nme'], $_POST['requested_book_id'], $row_bookname['book_nme'], $date, $unme);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            $records_error = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Books</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/issue_book_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="member_page.php">OLMS</a>
        </div>
    </div>


    <div class="book-rack">
        <?php

        $query_mem_name = "SELECT * FROM `olms_members` WHERE `mem_uname` = ?";
        $stmt_mem_name = $conn->prepare($query_mem_name);
        $stmt_mem_name->bind_param("s", $unme);
        $stmt_mem_name->execute();
        $result_mem_name = $stmt_mem_name->get_result();

        if ($result_mem_name->num_rows > 0) {
            // Fetch the row
            $row_name = $result_mem_name->fetch_assoc();

            // Access the name
            $mem_name = $row_name['mem_nme'];

            // Free the result set
            $result_mem_name->free();
        }

        $book_issued_query = "SELECT * FROM `olms_issue_requests` WHERE `requested_by` = '" . $mem_name . "';";
        $result_issued = $conn->query($book_issued_query);
        if ($result_issued) {
            $issue_request_count = mysqli_num_rows($result_issued);
            $result_issued->free();
        }

        $issued_qr = "SELECT * FROM `olms_issued` WHERE `issued_to` = '" . $mem_name . "';";
        $rslt_issued = $conn->query($issued_qr);
        if ($rslt_issued) {
            $issued_cnt = mysqli_num_rows($rslt_issued);
            $rslt_issued->free();
        }

        if ($rowCount === 0) { //If the row count from the books table is 0 then show 'No books available'
            echo "
            <div class='no-book'>
                <p>No books available</p>
            </div>
        ";
        } else if ($issued_cnt + $issue_request_count < 5) {
            $query = "SELECT * FROM `olms_books` ORDER BY `book_sl_no` ASC";
            $result = $conn->query($query);
            if ($result) {
                while ($row = $result->fetch_assoc()) {

                    $existing_request_query = "SELECT * FROM `olms_issue_requests` WHERE `requested_book_id` = '" . $row['book_id'] . "' AND `requested_by_uname` = '" . $unme . "'";
                    $result_existing = $conn->query($existing_request_query);
                    if ($result_existing) {
                        $row_exist = mysqli_num_rows($result_existing); //Stores number of rows from the table issue requests where the member has requested the book
                        $result_existing->free();
                    }

                    $query_member_name = "SELECT * FROM `olms_members` WHERE `mem_uname` = ?";
                    $stmt_member_name = $conn->prepare($query_member_name);
                    $stmt_member_name->bind_param("s", $unme);
                    $stmt_member_name->execute();
                    $result_member_name = $stmt_member_name->get_result();

                    if ($result_member_name->num_rows > 0) {
                        // Fetch the row
                        $row_name = $result_member_name->fetch_assoc();

                        // Access the name
                        $member_name = $row_name['mem_nme'];

                        // Free the result set
                        $result_member_name->free();
                    } else {
                        // Handle the case where no member with the given username is found
                        $member_name = 'Unknown Member';
                    }

                    $book_issued_query = "SELECT * FROM `olms_issued` WHERE `issued_to` = '" . $member_name . "' AND `issued_book_no` = '" . $row['book_id'] . "'";
                    $result_issued = $conn->query($book_issued_query);
                    if ($result_issued) {
                        $issued = mysqli_num_rows($result_issued);
                        $result_issued->free();
                    }

                    if ($row_exist === 0) {
                        if ($issued === 0) {
                            echo "
                            <div class='book'>
                                <p>Title: " . $row['book_nme'] . "</p>
                                <p>Author: " . $row['book_auth'] . "</p>
                                <p>Publication: " . $row['book_pub'] . "</p>
                                <p>Genre: " . $row['book_genre'] . "</p>

                                <div class='buttons'>
                                    <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                                        <input type='hidden' name='requested_mem_uname' value='" . $unme . "'>
                                        <input type='hidden' name='requested_book_id' value='" . $row['book_id'] . "'>
                                        <button type='submit' class='request-btn'>Request</button>
                                    </form>
                                </div>
                            </div>
                        ";
                        } else if ($issued === 1) {
                            //Don't show anything
                        }
                    }
                }
            }
        } else if ($issued_cnt + $issue_request_count >= 5) {
            if ($issued_cnt > 0) {
                echo "
                <div class='alert-books'>
                    <p>You have " . $issued_cnt . " books issued in your stack.</p>
                </div>
            ";
            }

            if ($issue_request_count > 0) {
                echo "
                <div class='alert-books'>
                    <p>You have requested " . $issue_request_count . " books</p>
                </div>
            ";
            }
        }

        ?>
    </div>
    <div class="alert">
        <?php
        // Check for alerts from the previous page
        if (isset($records_success)) {
            echo "<h3>" . $records_success . "</h3>";
        } else if (isset($records_error)) {
            echo "<h3>" . $records_error . "</h3>";
        }
        ?>
    </div>
</body>

</html>
<?php
$conn->close();
?>