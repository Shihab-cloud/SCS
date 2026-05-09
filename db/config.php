<?php
// 1. Fetch variables
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = (int)getenv('MYSQLPORT');

// 2. Debugging: If something is empty, it will show up in your Railway logs
if (empty($host) || empty($db)) {
    error_log("DATABASE ERROR: One or more environment variables are EMPTY.");
}

// 3. Connect. 
// Adding 'p:' before the host sometimes helps, but standard TCP is the goal.
$conn = new mysqli($host, $user, $pass, $db, $port);

// 4. Verification
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Database connection failed. Please check server logs.");
}
?>