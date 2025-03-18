<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titleToDelete = $_POST['title'];
    $xml = simplexml_load_file('data/books.xml') or die('Error: Cannot load XML file');

    // Find and remove the book
    foreach ($xml->book as $book) {
        if ((string)$book->title == $titleToDelete) {
            $dom = dom_import_simplexml($book);
            $dom->parentNode->removeChild($dom);
        }
    }

    // Save the updated XML
    $xml->asXML('data/books.xml');

    // Redirect back to admin page
    header('Location: admin_home.php');
    exit();
}
?>
