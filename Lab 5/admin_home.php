<?php
session_start();

// Load student and book data from XML files
$student_data = simplexml_load_file('data/student_data.xml') or die('Error: Cannot load student data');


// Handle book addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    
    $search_student_id = $_POST['student_id'];
    $curr_book_id = $_POST['book_id'];
    if ($search_student_id) {
        foreach ($student_data->student as $student) {
            if ((string)$student->id === $search_student_id) {
                if ($_POST['action'] === 'delete') {
                    
                    // Handle book deletion
                    $index = 0;
                    foreach ($student->book as $book) {
                        if ((string)$book->id === $curr_book_id) {
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
    <title>Admin Panel - Library System</title>
    <style>
        /* General styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            padding: 2rem;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* Form styling */
        form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 2rem;
        }
        input, button {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        input {
            flex: 1;
            min-width: 200px;
        }
        button {
            background-color: #0073e6;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #005bb5;
        }

        /* Card styles */
        .card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        .card-header {
            font-size: 1.3em;
            font-weight: bold;
            color: #34495e;
        }
        .card p {
            margin: 0.5rem 0;
        }
        .card-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }

        /* Buttons */
        .delete-btn, .remove-btn ,.logout-btn{
            background-color: #e74c3c;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        .delete-btn:hover, .remove-btn:hover {
            background-color: #c0392b;
        }
        .add-btn {
            background-color: #2ecc71;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        .add-btn:hover {
            background-color: #27ae60;
        }

        /* Book lists */
        .book-list, .student-list {
            margin-bottom: 2rem;
        }

        /* Flex grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
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
        <h1>📚 Admin Panel - Library Management</h1>

        <!-- Add Book Form -->
        <h2>Add a New Book</h2>
        <form method="POST" action="add_book.php">
            <input type="text" name="title" placeholder="Book Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="price" placeholder="Price" required>
            <input type="text" name="publish_date" placeholder="Publish Date" required>
            <input type="text" name="genre" placeholder="Genre" required>
            <input type="text" name="description" placeholder="Description" required>
            <button type="submit" class="add-btn">Add Book</button>
        </form>

        <!-- Search Student -->
        <h2>Search Student by ID</h2>
        <form method="GET">
            <input type="text" name="student_id" placeholder="Student ID" required>
            <button type="submit">Search</button>
        </form>

        <!-- Display Student's Books -->
        <div class="student-list">
            <h2>Student's Books</h2>
            <div class="grid">
                <?php
                $student_data = simplexml_load_file('data/student_data.xml') or die('Error: Cannot load student data');
                if (isset($_GET['student_id'])) {
                    $student_id = $_GET['student_id'];
                    $found = false;

                    foreach ($student_data->student as $student) {
                        if ((string)$student->id == $student_id) {
                            $found = true;
                            echo '<div class="card">';
                            echo '<div class="card-header">Student: ' . htmlspecialchars($student->name) . ' (ID: ' . $student->id . ')</div>';
                            
                            $books_count = count($student->book);
                            if ($books_count > 0) {
                                echo '<p><strong>Books:</strong></p>';
                                foreach ($student->book as $book) {
                                    echo '<p>' . htmlspecialchars($book->title) . '</p>';
                                    echo '<form method="POST" class="card-actions">';
                                    echo '<input type="hidden" name="student_id" value="' . $student_id . '">';
                                    echo '<input type="hidden" name="book_id" value="' . $book->id . '">';
                                    echo "<input type='hidden' name='action' value='delete'>";
                                    echo '<button type="submit" class="delete-btn">Remove</button>';
                                    echo '</form>';
                                }
                            }else{
                                echo '<h4>No books borrowed yet!</h4>';
                            }
                            echo '</div>';
                            break;
                        }
                    }

                    if (!$found) {
                        echo '<p>No student found with ID: ' . htmlspecialchars($student_id) . '</p>';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Display All Books -->
        <h2>Available Books</h2>
        <div class="grid book-list">
            <?php
            $book_data = simplexml_load_file('data/books.xml') or die('Error: Cannot load book data');
            foreach ($book_data->book as $book) {
                echo '<div class="card">';
                echo '<div class="card-header">' . htmlspecialchars($book->title) . '</div>';
                echo '<p><strong>Author:</strong> ' . htmlspecialchars($book->author) . '</p>';
                echo '<p><strong>Price:</strong> $' . htmlspecialchars($book->price) . '</p>';
                
                echo '<form method="POST" action="delete_book.php" class="card-actions">';
                echo '<input type="hidden" name="title" value="' . $book->title . '">';
                echo '<button type="submit" class="delete-btn">Remove</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>

