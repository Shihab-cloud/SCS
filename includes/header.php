<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty') {
  header("Location: /smart_cloud_system/login_step1.php"); exit();
}
$FACULTY_ID = $_SESSION['login_user'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SCS | Faculty</title>
  <link rel="stylesheet" href="/smart_cloud_system/assests/css/faculty.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=ADLaM+Display&display=swap" rel="stylesheet">
  <script src="/smart_cloud_system/assests/js/app.js" defer></script>
</head>
<body>
<div class="layout">