<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
  header("Location: /smart_cloud_system/login_step1.php"); exit();
}
$STUDENT_ID = $_SESSION['login_user'];  // Student ID from the session
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SCS | Student Dashboard</title>
  <link rel="stylesheet" href="/smart_cloud_system/assests/css/student.css" />
  <script src="/smart_cloud_system/assests/js/app.js" defer></script>
</head>
<body>
<div class="layout">