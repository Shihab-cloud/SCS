<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
  header("Location: ../login_step1.php"); exit();
}
$STUDENT_ID = $_SESSION['login_user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SCS | Student Dashboard</title>
  <link rel="stylesheet" href="../assests/css/student.css" />
  <script src="../assests/js/app.js" defer></script>
</head>
<body>
<div class="layout">