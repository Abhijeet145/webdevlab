<?php
session_start();
// Load XML file containing admin credentials
$xml = simplexml_load_file('data/student_data.xml') or die('Error: Cannot load admin data');

// Get the submitted admin ID and password
$student_id = $_POST['student_id'];
$password = $_POST['password'];


// Verify credentials
$valid = false;
foreach ($xml->student as $student) {
    if ($student->id == $student_id && $student->password == $password) {
        $_SESSION['student_id'] = $student_id;
        $valid = true;
        break;
    }
}

// Redirect or show message based on validation
if ($valid) {
    header('Location: http://localhost:3000/Lab%205/student_home.php');
} else {
    header('Loaction: http://localhost:3000/Lab%205/student_login.html');
}
?>