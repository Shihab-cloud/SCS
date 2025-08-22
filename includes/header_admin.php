<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /smart_cloud_system/login_step1.php");
    exit();
}
$ADMIN_ID = $_SESSION['login_user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SCS | Admin Dashboard</title>
    <link rel="stylesheet" href="/smart_cloud_system/assests/css/admin.css" />
    <script src="/smart_cloud_system/assests/js/app.js" defer></script>
</head>
<body>
    <div class="layout">