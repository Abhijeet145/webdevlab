<?php
// Load XML file containing admin credentials
$xml = simplexml_load_file('data/admin_data.xml') or die('Error: Cannot load admin data');

// Get the submitted admin ID and password
$admin_id = $_POST['admin_id'];
$password = $_POST['password'];

// Verify credentials
$valid = false;
foreach ($xml->admin as $admin) {
    if ($admin->id == $admin_id && $admin->password == $password) {
        $valid = true;
        break;
    }
}

// Redirect or show message based on validation
if ($valid) {
    header('Location: http://localhost:3000/Lab%205/admin_home.php');
} else {
    // $message = "Incorrect Id or Password!";
    // echo "<script type='text/javascript'>alert('$message');</script>";
    header('Location: http://localhost:3000/Lab%205/index.html');
}
?>