<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'administrator')) {
    header("Location: index.html");
    exit();
}
$regdate = date('d-m-Y');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    include 'php_utils/_dbConnect.php';

    //Get number of rows in librarians table
    $lib_query_count = "SELECT COUNT(*) as librarian_row_count FROM olms_librarian";
    $lib_result_count = $conn->query($lib_query_count);
    if ($lib_result_count) {
        $lib_row = $lib_result_count->fetch_assoc();
        $librowCount = $lib_row['librarian_row_count'];
        $lib_result_count->free();
    }

    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $email = $_POST["mail"];
    $address = $_POST["address"];
    $reg_date = $_POST["regdate"];
    $photo_path = 'profile_pics/Librarians/' . basename($_FILES['photo']['name']);
    $username = $_POST["username"];
    $password = $_POST["password"];
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $lib_id = 'L' . ($librowCount + 1);

    if ($name != '' && $phone != '' && $email != '' && $address != '' && $reg_date != '' && $photo_path != '' && $username != '' && $hashed_pass != '' && $lib_id != '') {

        $query_to_check_email = "SELECT * FROM `olms_librarian` WHERE `lib_mail` = ?";
        $stmt1 = $conn->prepare($query_to_check_email);
        $stmt1->bind_param("s", $email);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $row_email = $result1->fetch_assoc();
        $stmt1->close();
        if ($row_email > 0) {
            $email_exists_message = "Email already exists";
        } else {
            $query_to_check_uname = "SELECT * FROM `olms_librarian` WHERE `lib_uname` = ?";
            $stmt2 = $conn->prepare($query_to_check_uname);
            $stmt2->bind_param("s", $username);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $row_uname = $result2->fetch_assoc();
            $stmt2->close();
            if ($row_uname > 0) {
                $username_exists_message = "Username already exists";
            } else {
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                    $table_insert_sql_query = "INSERT INTO `olms_librarian`(`lib_nme`, `lib_id`, `lib_phn`, `lib_mail`, `lib_addr`, `reg_date`, `img_path`, `lib_uname`, `lib_pword`) VALUES ('$name', '$lib_id', '$phone', '$email', '$address', '$reg_date', '$photo_path', '$username', '$hashed_pass')";

                    if ($conn->query($table_insert_sql_query)) {
                        header("Location: admin_page.php");
                        exit();
                    } else {
                        $records_error = "Error: " . $conn->error;
                    }
                } else {
                    $photo_error = "Unable to upload the file.";
                }
            }
        }
    } else {
        $blank_entries = "Cannot insert blank entries";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Signup</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/signup_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>
    </div>

    <div class="form">
        <form action="librarian_signup.php" method="post" enctype="multipart/form-data">
            <div class="heading">
                <h2>Add Librarian</h2>
            </div>

            <div class="inner-form">
                <label for="name">Name:</label>
                <input type="text" name="name" placeholder="Enter name">
                <label for="phone">Phone:</label>
                <input type="text" name="phone" placeholder="Enter phone number">
                <label for="mail">Email:</label>
                <input type="email" name="mail" placeholder="Enter email">
                <label for="address">Address:</label>
                <input type="text" name="address" placeholder="Enter address">
                <input type="hidden" name="regdate" value="<?php echo $regdate; ?>">
                <label for="photo">Profile Photo:</label>
                <input type="file" name="photo">
                <label for="username">Choose Username:</label>
                <input type="text" name="username" placeholder="Choose username">
                <label for="password">Choose Password:</label>
                <input type="password" name="password" placeholder="Enter the password">

                <button type="submit">Register</button>

                <?php
                if (isset($email_exists_message)) {
                    echo '<button disabled>' . $email_exists_message . '</button>';
                } else if (isset($username_exists_message)) {
                    echo '<button disabled>' . $username_exists_message . '</button>';
                } else if (isset($records_success)) {
                    echo '<button disabled>' . $records_success . '</button>';
                } else if (isset($records_error)) {
                    echo '<button disabled>' . $records_error . '</button>';
                } else if (isset($photo_error)) {
                    echo '<button disabled>' . $photo_error . '</button>';
                } else if (isset($blank_entries)) {
                    echo '<button disabled>' . $blank_entries . '</button>';
                } else {
                }
                ?>
            </div>
        </form>
    </div>
</body>

</html>