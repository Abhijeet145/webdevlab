<?php
// Load XML file containing admin credentials
$xml = simplexml_load_file('data/student_data.xml') or die('Error: Cannot load admin data');

// Get the submitted admin ID and password
$student_id = $_POST['student_id'];
$password = $_POST['password'];
$student_name = $_POST['student_name'];
$student_email = $_POST['student_email'];

// $doc = new DOMDocument();
$student = $xml->addChild('student');
$student->addChild('id', $student_id);
$student->addChild('name', $student_name);
$student->addChild('email', $student_email);
$student->addChild('password', $password);


if(file_put_contents('data/student_data.xml',$xml->asXML())){
    // echo 'Registered Successfully!';
    header('Location: http://localhost:3000/Lab%205/student_login.html');
}else{
    echo 'Registration failed!';
}

?>