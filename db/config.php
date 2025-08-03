<?php
$host = "localhost";
$user = "root";             // Default for XAMPP
$pass = "";                 // Default is empty
$dbname = "smart_campus";   // Use the name from your .sql file

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>