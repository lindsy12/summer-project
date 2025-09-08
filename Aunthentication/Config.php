<?php
$host = "localhost";
$user = "root";   // change if you have another user
$pass = "";       // add your MySQL password if set
$db   = "skincare_db";


$conn = new mysqli($host, $user, $pass, $db);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>