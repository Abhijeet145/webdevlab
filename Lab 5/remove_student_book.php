<?php
if ($_POST['action'] === 'delete') {
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
?>