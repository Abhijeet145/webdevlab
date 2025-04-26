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
$name = $_POST['name'];
$email = $_POST['email'];

$entry = $xml->addChild('entry');
$entry->addChild('id', $user_id);
$entry->addChild('name', $name);
$entry->addChild('email', $email);
$entry->addChild('password', $password);

$valid = false;
if($user_type == 'doctor'){
    $entry->addChild('specialization', $_POST['specialization']);
    $entry->addChild('available', "Not Available");
    $entry->addChild('availability_time', "N/A");
    $xml->asXML('../data/doctor_data.xml');
    $valid = true;
}elseif($user_type == 'nurse'){
    $xml->asXML('../data/nurse_data.xml');
    $valid = true;
}elseif($user_type == 'patient'){
    $entry->addChild('medical_history', $_POST['medical_history']);
    $entry->addChild('nurse_assigned', "N/A");
    $entry->addChild('treatements', "N/A");
    $entry->addChild('appointments', "N/A");
    $xml->asXML('../data/patient_data.xml');
    $valid = true;
}else{
    echo "Invalid user type.";
    exit();
}
if ($valid) {
    header('Location: ../index.html');
} else {
    echo "Registration failed!";
    exit();
}
?>