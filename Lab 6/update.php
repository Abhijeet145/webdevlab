<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $column = $_POST['column'];
    $value = $_POST['value'];

    $xml = simplexml_load_file("data/books.xml");

    foreach ($xml->book as $book) {
        if ($book->id == $id) {
            $book->$column = $value;
            break;
        }
    }

    $xml->asXML("data/books.xml");
    echo "Updated successfully!";
}
?>

