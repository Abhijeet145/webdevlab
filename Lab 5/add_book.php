<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $publish_date = $_POST['publish_data'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];

    // Load the XML file or create a new one if it doesn't exist
    if (file_exists('data/books.xml')) {
        $xml = simplexml_load_file('data/books.xml');
    } else {
        $xml = new SimpleXMLElement('<catalog></catalog>');
    }

    // Add a new book
    $book = $xml->addChild('book');
    $book->addChild('title', htmlspecialchars($title));
    $book->addChild('author', htmlspecialchars($author));
    $book->addChild('price', htmlspecialchars($price));
    $book->addChild('publish_date', htmlspecialchars($publish_date));
    $book->addChild('genre', htmlspecialchars($genre));
    $book->addChild('description', htmlspecialchars($description));

    // Save the updated XML
    $xml->asXML('data/books.xml');

    // Redirect back to admin page
    header('Location: admin_home.php');
    exit();
}
?>