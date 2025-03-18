<?php
session_start();

// Load student and book data from XML files
$student_data = simplexml_load_file('data/student_data.xml') or die('Error: Cannot load student data');
$book_data = simplexml_load_file('data/books.xml') or die('Error: Cannot load book data');

// Get student_id from session (set after login)
$logged_in_student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : null;

// Handle book addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $book_id = $_POST['book_id'];
    $book_title = $_POST['book_title'];

    if ($logged_in_student_id) {
        foreach ($student_data->student as $student) {
            if ((string)$student->id === $logged_in_student_id) {
                if ($_POST['action'] === 'add') {
                    $current_books = count($student->book);
                    $is_duplicate = false;

                    // Check for duplicate book
                    foreach ($student->book as $book) {
                        if ((string)$book->id === $book_id) {
                            $is_duplicate = true;
                            echo "<script>alert('You have already added this book!');</script>";
                            break;
                        }
                    }

                    if (!$is_duplicate && $current_books < 3) {
                        $newBook = $student->addChild('book');
                        $newBook->addChild('id', $book_id);
                        $newBook->addChild('title', $book_title);
                        $student_data->asXML('data/student_data.xml');
                        header('Location: ' . $_SERVER['PHP_SELF']);
                        exit();
                    } elseif ($current_books >= 3) {
                        echo "<script>alert('You can only add up to 3 books!');</script>";
                    }
                } elseif ($_POST['action'] === 'delete') {
                    // Handle book deletion
                    $index = 0;
                    foreach ($student->book as $book) {
                        if ((string)$book->id === $book_id) {
                            unset($student->book[$index]);
                            $student_data->asXML('data/student_data.xml');
                            header('Location: ' . $_SERVER['PHP_SELF']);
                            exit();
                        }
                        $index++;
                    }
                }
                break;
            }
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: student_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Data and Books</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 2rem;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            max-width: 800px;
            margin: 0 auto;
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #0073e6;
            color: white;
        }
        .add-btn, .delete-btn, .logout-btn {
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .add-btn {
            background-color: #28a745;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .logout-btn {
            background-color: #343a40;
        }
        .logout-container {
            text-align: right;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout-container">
            <a href="?logout=true" class="logout-btn">Logout</a>
        </div>
        <h1>Welcome, Student ID: <?php echo htmlspecialchars($logged_in_student_id); ?></h1>
        
        <h2>Your Books</h2>
        <table>
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($student_data->student as $student) {
                    if ((string)$student->id === $logged_in_student_id) {
                        if (count($student->book) > 0) {
                            foreach ($student->book as $book) {
                                echo "<tr>
                                    <td>{$book->id}</td>
                                    <td>{$book->title}</td>
                                    <td>
                                        <form method='POST'>
                                            <input type='hidden' name='book_id' value='{$book->id}'>
                                            <input type='hidden' name='action' value='delete'>
                                            <button type='submit' class='delete-btn'>Remove</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo '<tr><td colspan="3">You have not added any books yet.</td></tr>';
                        }
                        break;
                    }
                }
                ?>
            </tbody>
        </table>

        <h2>Available Books</h2>
        <table>
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($book_data->book as $book): ?>
                    <tr>
                        <td><?php echo $book->id; ?></td>
                        <td><?php echo $book->title; ?></td>
                        <td><?php echo $book->author; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="book_id" value="<?php echo $book->id; ?>">
                                <input type="hidden" name="book_title" value="<?php echo $book->title; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="add-btn">Add</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
