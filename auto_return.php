<?php
session_start();
if (!(isset($_SESSION['aloggedin']) && $_SESSION['aloggedin'] == true && isset($_SESSION['ausername']) && $_SESSION['ausername'] == 'admin')) {
    header("location: admin_login.php");
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Load Composer's autoloader
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';

include 'php_utils/_dbConnect.php';

// Get number of rows in transaction table in order to use the count to initialize the variable $transaction_id
$trans_query_count = "SELECT COUNT(*) as trans_row_count FROM olms_transactions";
$trans_result_count = $conn->query($trans_query_count);
if ($trans_result_count) {
    $trans_row = $trans_result_count->fetch_assoc();
    $transrowCount = $trans_row['trans_row_count'];
    $trans_result_count->free();
}

$transaction_id = 'T' . ($transrowCount + 1);
$tran_type = "Return";
$today = new DateTime();

$tday = clone $today;
$tdy = clone $today;

// echo $today->format('d-m-Y') . PHP_EOL;
// echo "Today's date: " . $tdy->format('d-m-Y') . PHP_EOL;
// echo "<br>";
// echo "<br>";
// echo $tday->format('d-m-Y') . PHP_EOL;

$tday->modify("+2 days"); // Add 2 days to the current date
$twodayslater = $tday->format('d-m-Y');

$tdy->modify("+1 days"); // Add 1 day to the current date
$onedaylater = $tdy->format('d-m-Y');

// echo "Tomorrow's date: " . $onedaylater . PHP_EOL;
// echo "<br>";
// echo "<br>";
// echo "Day after Tomorrow's date: " . $twodayslater . PHP_EOL;
// echo "<br>";
// echo "<br>";

$issue = "SELECT * FROM `olms_issued`";
$result_issue = $conn->query($issue);
$num_rows = ($result_issue) ? $result_issue->num_rows : 0;
if ($result_issue) {
    if ($num_rows != 0) {
        while ($row = $result_issue->fetch_assoc()) {
            $iud = DateTime::createFromFormat('d-m-Y', $row['issued_upto']);
            $issued_upto_date = $iud->format('d-m-Y');

            $twodayslater = $tday->format('d-m-Y');
            $onedaylater = $tdy->format('d-m-Y');

            if ($issued_upto_date === $today) {
                $mem_id_qry = "SELECT * FROM `olms_members` WHERE `mem_nme` = '" . $row['issued_to'] . "'";
                $mem_res = mysqli_query($conn, $mem_id_qry);
                $memberRow = mysqli_fetch_array($mem_res);
                $memberId = $memberRow['mem_id'];

                $lib_id_qry = "SELECT * FROM `olms_librarian` WHERE `lib_uname` = '" . $row['issued_by'] . "'";
                $lib_res = mysqli_query($conn, $lib_id_qry);

                // Check if the query was successful and returned rows
                if ($lib_res && mysqli_num_rows($lib_res) > 0) {
                    $librarianRow = mysqli_fetch_array($lib_res);
                    $librarianId = $librarianRow['lib_id'];
                    $librarianName = $librarianRow['lib_nme'];
                }

                $book_id_qry = "SELECT * FROM `olms_books` WHERE `book_id` = '" . $row['issued_book_no'] . "'";
                $book_res = mysqli_query($conn, $book_id_qry);
                $bookRow = mysqli_fetch_array($book_res);
                $bookName = $bookRow['book_nme'];

                $transaction_sql_query = "INSERT INTO `olms_transactions`(`transaction_id`, `transaction_type`, `transaction_date`, `book_id`, `book_name`, `member_id`, `member_name`, `librarian_id`, `librarian_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt_transaction = $conn->prepare($transaction_sql_query);
                $stmt_transaction->bind_param("sssssssss", $transaction_id, $tran_type, $today->format('d-m-Y'), $row['issued_book_no'], $bookName, $memberId, $row['issued_to'], $librarianId, $librarianName);
                $stmt_transaction->execute();  // -------1

                $delete_query = "DELETE FROM `olms_issued` WHERE `issued_book_no` = ? AND `issued_to` = ?";
                $stmt_dlt = $conn->prepare($delete_query);
                $stmt_dlt->bind_param("ss", $row['issued_book_no'], $row['issued_to']);
                $stmt_dlt->execute(); // ------------- 2
            } else if ($issued_upto_date === $twodayslater) {

                $issued_to = $row['issued_to'];

                $qry1 = "SELECT * FROM `olms_members` WHERE `mem_nme` = '" . $row['issued_to'] . "'";
                $res1 = mysqli_query($conn, $qry1);
                $Row = mysqli_fetch_array($res1);
                $email = $Row['mem_mail'];

                $qry3 = "SELECT * FROM `olms_books` WHERE `book_id` = '" . $row['issued_book_no'] . "'";
                $res3 = mysqli_query($conn, $qry3);
                $bRow = mysqli_fetch_array($res3);
                $bName = $bRow['book_nme'];

                // echo $issued_to;
                // echo '<br>';
                // echo $email;
                // echo '<br>';
                // echo $bName;
                // echo '<br>';

                $_SESSION['temp'] = $email;
                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);

                try {
                    //Server settings
                    $mail->isSMTP(); //Send using SMTP
                    $mail->Host       = 'smtp.gmail.com'; //Set the SMTP server to send through
                    $mail->SMTPAuth   = true; //Enable SMTP authentication
                    $mail->Username   = 'mailankandgp@gmail.com'; //SMTP username
                    $mail->Password   = 'xkwophkjlgljzxcb'; //SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
                    $mail->Port       = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                    //Recipients
                    $mail->setFrom('mailankandgp@gmail.com', 'Gentle reminder - OLMS');
                    $mail->addAddress($email);     //Add a recipient

                    //Content
                    $mail->isHTML(true); //Set email format to HTML
                    $mail->Subject = 'Gentle Reminder - only 2 days left for the book '. $bName .'!!';
                    $mail->Body    = "Heyy " . $issued_to . ", the book " . $bName . " which was issued to you from OLMS will be returned in 2 days, thank you!!";

                    $mail->send();
                    // echo "Two day's reminder has been sent";
                } catch (Exception $e) {
                    echo "Two day's reminder could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else if ($issued_upto_date === $onedaylater) {

                $issued_to = $row['issued_to'];

                echo $issued_to;

                $qry2 = "SELECT * FROM `olms_members` WHERE `mem_nme` = '" . $row['issued_to'] . "'";
                $res2 = mysqli_query($conn, $qry2);
                $Row = mysqli_fetch_array($res2);
                $email = $Row['mem_mail'];

                $qry4 = "SELECT * FROM `olms_books` WHERE `book_id` = '" . $row['issued_book_no'] . "'";
                $res4 = mysqli_query($conn, $qry4);
                $bRow = mysqli_fetch_array($res4);
                $bName = $bRow['book_nme'];

                // echo $issued_to;
                // echo '<br>';
                // echo $email;
                // echo '<br>';
                // echo $bName;
                // echo '<br>';
                // echo '<br>';

                $_SESSION['temp'] = $email;
                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);

                try {
                    //Server settings
                    $mail->isSMTP(); //Send using SMTP
                    $mail->Host       = 'smtp.gmail.com'; //Set the SMTP server to send through
                    $mail->SMTPAuth   = true; //Enable SMTP authentication
                    $mail->Username   = 'mailankandgp@gmail.com'; //SMTP username
                    $mail->Password   = 'xkwophkjlgljzxcb'; //SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
                    $mail->Port       = 465; //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                    //Recipients
                    $mail->setFrom('mailankandgp@gmail.com', 'Gentle Reminder - OLMS');
                    $mail->addAddress($email);     //Add a recipient

                    //Content
                    $mail->isHTML(true); //Set email format to HTML
                    $mail->Subject = 'Gentle Reminder - only 1 day left for the book '. $bName .'!!';
                    $mail->Body    = "Heyy ".$issued_to.", the book ". $bName ." which was issued to you from OLMS will be returned in 2 days, thank you!!";

                    $mail->send();
                    // echo "One day's reminder has been sent";
                } catch (Exception $e) {
                    echo "One day's reminder could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }
        }
    }
}
// header("Location: admin_page.php");
// exit();
?>
