<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'administrator' && $_SESSION['user_role'] !== 'librarian')) {
    header("Location: index.html");
    exit();
}

include 'php_utils/_dbConnect.php';

$query_count = "SELECT COUNT(*) as row_count FROM olms_books";
$result_count = $conn->query($query_count);
if ($result_count) {
    $row = $result_count->fetch_assoc();
    $rowCount = $row['row_count'];
    $result_count->free();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_book_id'])) {
    $delete_book_id = $_POST['delete_book_id'];
    $delete_query = "DELETE FROM `olms_books` WHERE `book_id` = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("s", $delete_book_id);

    if ($stmt->execute()) {
        $delete_success = "Book deleted successfully";
        $stmt->close();
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } else {
        $delete_error = "Error: " . $stmt->error;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rc = $_POST["row_count"];
    $id = 'B' . ($rc + 1);
    $name = $_POST["name"];
    $auth_name = $_POST["author"];
    $pub_name = $_POST["publication"];
    $genre = $_POST["genre"];
    $stock = $_POST["stock"];

    if ($id != '' && $name != '' && $auth_name != '' && $pub_name != '' && $genre != '' && $stock != '') {
        require 'php_utils/_dbConnect.php';
        $book_insert_sql_query = "INSERT INTO `olms_books`(`book_id`, `book_nme`, `book_auth`, `book_pub`, `book_genre`, `book_stock`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($book_insert_sql_query);
        $stmt->bind_param("ssssss", $id, $name, $auth_name, $pub_name, $genre, $stock);

        if ($stmt->execute()) {
            $records_success = "Book inserted successfully";
            $stmt->close();
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        } else {
            $records_error = "Error: " . $stmt->error;
        }
    } else {
        $blank_entries = "Can't submit blank entries";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books</title>
    <link rel="stylesheet" href="CSS/nav_style.css">
    <link rel="stylesheet" href="CSS/book_style.css">
    <link rel="shortcut icon" href="favicon/favicon.ico" type="image/x-icon">
    <script>
        window.onload = function() {
            window.history.pushState({}, "");
        };
        window.addEventListener('popstate', function(event) {
            var referrer = document.referrer;
            if (referrer.includes("admin_page.php") && "<?php echo $_SESSION['user_role']; ?>" === "administrator") {
                window.location.href = "admin_page.php";
            } else if (referrer.includes("librarian_page.php") && "<?php echo $_SESSION['user_role']; ?>" === "librarian") {
                window.location.href = "librarian_page.php";
            }
        });
    </script>
</head>

<body>
    <div class="navbar">
        <div class="icon">
            <a href="#">OLMS</a>
        </div>
    </div>

    <div class="container">
        <div class="book-rack">
            <?php
            if ($rowCount == 0) {
                echo "
            <div class='no-book'>
                <p>No books available</p>
            </div>
        ";
            } else {
                $query = "SELECT * FROM `olms_books` ORDER BY `book_sl_no` DESC"; // Assuming 'id' is your primary key
                $result = $conn->query($query);
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo "
                <div class='book'>
                    <p>Book ID: " . $row['book_id'] . "</p>
                    <p>Title: " . $row['book_nme'] . "</p>
                    <p>Author: " . $row['book_auth'] . "</p>
                    <p>Publication: " . $row['book_pub'] . "</p>
                    <p>Genre: " . $row['book_genre'] . "</p>
                    <p>Stock: " . $row['book_stock'] . "</p>

                    <div class='buttons'>
                        <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                            <input type='hidden' name='edit_book_id' value='" . $row['book_id'] . "'>
                            <button type='submit' class='edit-btn'>Edit Details</button>
                        </form>
                        <form action='{$_SERVER['REQUEST_URI']}' method='post'>
                            <input type='hidden' name='delete_book_id' value='" . $row['book_id'] . "'>
                            <button type='submit' class='delete-btn'>Delete</button>
                        </form>
                    </div>
                </div>
            ";
                    }
                }
            }
            ?>
        </div>

        <div class="form">
            <form action="books.php" method="post" enctype="multipart/form-data">
                <div class="heading">
                    <h2>Add Books</h2>
                </div>

                <div class="inner-form">
                    <input type="hidden" name="row_count" value="<?php echo $rowCount; ?>">
                    <label for="name">Book Name:</label>
                    <input type="text" name="name" placeholder="Enter book name">
                    <label for="author">Author Name:</label>
                    <input type="text" name="author" placeholder="Enter author name">
                    <label for="publication">Publication:</label>
                    <input type="text" name="publication" placeholder="Enter publication name">
                    <label for="genre">Genre:</label>
                    <select name="genre">
                        <option value="">Select the genre</option>
                        <option value="Fiction">Fiction</option>
                        <option value="Mystery & Thriller">Mystery & Thriller</option>
                        <option value="Science Fiction & Fantasy">Science Fiction & Fantasy</option>
                        <option value="Romance">Romance</option>
                        <option value="Historical Fiction">Historical Fiction</option>
                        <option value="Biography & Memoir">Biography & Memoir</option>
                        <option value="Non-Fiction">Non-Fiction</option>
                        <option value="Self-Help & Personal Development">Self-Help & Personal Development</option>
                        <option value="Children's & Young Adult">Children's & Young Adult</option>
                        <option value="History">History</option>
                        <option value="Travel">Travel</option>
                        <option value="Science">Science</option>
                        <option value="Business">Business</option>
                    </select>
                    <label for="stock">Stock:</label>
                    <input type="text" name="stock" placeholder="Enter the stock number">

                    <button type="submit">Add Book</button>

                    <?php
                    if (isset($records_error)) {
                        echo '<button disabled>' . $records_error . '</button>';
                    } else if (isset($blank_entries)) {
                        echo '<button disabled>' . $blank_entries . '</button>';
                    }
                    ?>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
<?php
$conn->close();
?>