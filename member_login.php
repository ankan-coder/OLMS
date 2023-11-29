<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'php_utils/_dbConnect.php';
    $mem_enter_username = $_POST["musername"];
    $mem_enter_password = $_POST["mpassword"];
    $sql1 = "SELECT `mem_pword` FROM `olms_members` WHERE `mem_uname` = '$mem_enter_username'";
    $result1 = mysqli_query($conn, $sql1);
    if ($result1) {
        $row1 = $result1->fetch_assoc();
        $num = mysqli_num_rows($result1);
        $pwd = $row1['mem_pword'];
    }

    if ($num == 1 && password_verify($mem_enter_password, $pwd)) {
        session_start();
        $_SESSION['mloggedin'] = true;
        $_SESSION['musername'] = $mem_enter_username;
        $_SESSION['user_role'] = 'member';
        header("Location: member_page.php");
        exit;

        // echo "Logged in!";
    } else {
        $success_status = "Invalid username or password";
        echo $success_status;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/login_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="index.html">OLMS</a>
        </div>

        <nav>
            <li><a href="about.html">About us</a></li>

            <li><a href="member_signup.php">Get Membership</a></li>
            <li class="dropdown">
                <a href="#">Login</a>
                <div class="dropdown-content">
                    <a href="admin_login.php">Administrator</a>
                    <a href="librarian_login.php">Librarian</a>
                </div>
            </li>

            <li><a href="contact.php">Contact us</a></li>
        </nav>
    </div>

    <div class="form">
        <form action="member_login.php" method="post" enctype="multipart/form-data">
            <div class="heading">
                <h2>Member login</h2>
            </div>

            <div class="inner-form">
                <label for="username">Username:</label>
                <input type="text" name="musername" placeholder="Enter the username">
                <label for="password">Password:</label>
                <input type="password" name="mpassword" placeholder="Enter the password">

                <button type="submit">Sign In</button>

                <?php
                if (isset($success_status)) {
                    echo '<button disabled>' . $success_status . '</button>';
                }
                ?>
            </div>
        </form>
        <!-- <a href="forgot-password.php"><button>Forgot your password? Recover it!</button></a> -->
    </div>
</body>

</html>