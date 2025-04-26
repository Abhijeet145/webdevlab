<?php
session_start();

$user_type = $_POST['user_type'];
$xml = null;
if ($user_type == 'doctor') {
    $xml = simplexml_load_file('../data/doctor_data.xml') or die('Error: Cannot load admin data');
} elseif ($user_type == 'nurse') {
    $xml = simplexml_load_file('../data/nurse_data.xml') or die('Error: Cannot load nurse data');
} elseif ($user_type == 'patient') {
    $xml = simplexml_load_file('../data/patient_data.xml') or die('Error: Cannot load user data');
} else {
    echo "Invalid user type.";
    exit();
}

// Get the submitted admin ID and password
$user_id = $_POST['id'];
$password = $_POST['password'];


// Verify credentials
$valid = false;
foreach ($xml->entry as $user) {
    if ($user->id == $user_id && $user->password == $password) {
        $_SESSION['uid'] = $user_id;
        $valid = true;
        break;
    }
}

// Redirect or show message based on validation
if ($valid) {
    if ($user_type == 'doctor') {
        header('Location: ..//doctor_home.php');
    } elseif ($user_type == 'nurse') {
        header('Location: ../nurse_home.php');
    } elseif ($user_type == 'patient') {
        header('Location: ../patient_home.php');
    } else {
        echo "Invalid user type.";
        exit();
    }
} else {
    echo "Invalid credentials!";
    header('Loaction: login.html');
}
?>