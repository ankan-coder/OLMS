<?php
$regdate = date('d-m-Y');
$email_exists_message = $username_exists_message = $records_success = $records_error = $photo_error = $blank_entries = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'php_utils/_dbConnect.php';
    $name = $_POST["name"];

    //Get number of rows in members table
    $mem_query_count = "SELECT COUNT(*) as member_row_count FROM olms_members";
    $mem_result_count = $conn->query($mem_query_count);
    if ($mem_result_count) {
        $mem_row = $mem_result_count->fetch_assoc();
        $memrowCount = $mem_row['member_row_count'];
        $mem_result_count->free();
    }

    $phone = $_POST["phone"];
    $email = $_POST["mail"];
    $address = $_POST["address"];
    $reg_date = $regdate; // Use the $regdate variable you defined
    $photo_path = 'profile_pics/Members/' . basename($_FILES['photo']['name']);
    $username = $_POST["username"];
    $password = $_POST["password"];
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $mem_id = 'M' . ($memrowCount + 1);

    if (!empty($name) && !empty($phone) && !empty($email) && !empty($address) && !empty($reg_date) && !empty($photo_path) && !empty($username) && !empty($hashed_pass) && !empty($mem_id)) {

        $query_to_check_email = "SELECT * FROM `olms_members` WHERE `mem_mail` = ?";
        $stmt1 = $conn->prepare($query_to_check_email);
        $stmt1->bind_param("s", $email);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $row_email = $result1->fetch_assoc();
        $stmt1->close();

        if ($row_email) {
            $email_exists_message = "Email already exists";
        } else {
            $query_to_check_uname = "SELECT * FROM `olms_members` WHERE `mem_uname` = ?";
            $stmt2 = $conn->prepare($query_to_check_uname);
            $stmt2->bind_param("s", $username);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $row_uname = $result2->fetch_assoc();
            $stmt2->close();

            if ($row_uname) {
                $username_exists_message = "Username already exists";
            } else {
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
                    $table_insert_sql_query = "INSERT INTO `olms_members` (`mem_id`, `mem_nme`, `mem_phn`, `mem_mail`, `mem_addr`, `reg_date`, `img_path`, `mem_uname`, `mem_pword`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt3 = $conn->prepare($table_insert_sql_query);
                    $stmt3->bind_param("sssssssss", $mem_id, $name, $phone, $email, $address, $reg_date, $photo_path, $username, $hashed_pass);

                    if ($stmt3->execute()) {
                        $records_success = "Record inserted successfully";
                        header("Location: member_login.php");
                        exit();
                    } else {
                        $records_error = "Error: " . $stmt3->error;
                    }
                    $stmt3->close();
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
    <title>Member Signup</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/signup_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="index.html">OLMS</a>
        </div>

        <nav>
            <li><a href="about.html">About us</a></li>

            <li class="dropdown">
                <a href="#">Login</a>
                <div class="dropdown-content">
                    <a href="admin_login.php">Administrator</a>
                    <a href="librarian_login.php">Librarian</a>
                    <a href="member_login.php">Member</a>
                </div>
            </li>

            <li><a href="contact.php">Contact us</a></li>
        </nav>
    </div>

    <div class="form">
        <form action="member_signup.php" method="POST" enctype="multipart/form-data">
            <div class="heading">
                <h2>Register as Member</h2>
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
                <input type="hidden" name="regdate">
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