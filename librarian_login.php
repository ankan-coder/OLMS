<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'php_utils/_dbConnect.php';
    $lib_enter_username = $_POST["lusername"];
    $lib_enter_password = $_POST["lpassword"];
    $sql1 = "SELECT `lib_pword` FROM `olms_librarian` WHERE `lib_uname` = '$lib_enter_username'";
    $result1 = mysqli_query($conn, $sql1);
    if ($result1) {
        $row1 = $result1->fetch_assoc();
        $num = mysqli_num_rows($result1);
        $pwd = $row1['lib_pword'];
    }

    if ($num == 1 && password_verify($lib_enter_password, $pwd)) {
        session_start();
        $_SESSION['lloggedin'] = true;
        $_SESSION['lusername'] = $lib_enter_username;
        $_SESSION['user_role'] = 'librarian';
        header("Location: librarian_page.php");
        exit;

        // echo "Logged in!";
    } else {
        echo '
            <script>
                alert("Invalid username or password");
            </script?
        ';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Login</title>
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
                    <a href="member_login.php">Member</a>
                </div>
            </li>

            <li><a href="contact.php">Contact us</a></li>
        </nav>
    </div>

    <div class="form">
        <form action="librarian_login.php" method="post" enctype="multipart/form-data">
            <div class="heading">
                <h2>Librarian login</h2>
            </div>

            <div class="inner-form">
                <label for="username">Username:</label>
                <input type="text" name="lusername" placeholder="Enter the username">
                <label for="password">Password:</label>
                <input type="password" name="lpassword" placeholder="Enter the password">

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