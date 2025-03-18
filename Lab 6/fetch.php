<?php
$xml = simplexml_load_file("data/books.xml");

if (isset($_GET['id'])) {
    // Fetch a specific book by ID
    $id = $_GET['id'];
    foreach ($xml->book as $book) {
        if ($book->id == $id) {
            echo json_encode($book);
            exit;
        }
    }
    echo json_encode([]);
} elseif (isset($_GET['column'])) {
    // Fetch specific column (title or author)
    $column = $_GET['column'];
    $result = [];
    foreach ($xml->book as $book) {
        $result[] = [
            'id' => (string) $book->id,
            $column => (string) $book->$column
        ];
    }
    echo json_encode($result);
} else {
    // Fetch all books
    $books = [];
    foreach ($xml->book as $book) {
        $books[] = [
            'id' => (string) $book->id,
            'title' => (string) $book->title,
            'author' => (string) $book->author,
            'year' => (string) $book->publish_date
        ];
    }
    echo json_encode($books);
}
?>
