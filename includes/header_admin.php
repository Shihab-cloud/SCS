<?php
// 1. Safe Session Initialization
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Security Check (Absolute path / to prevent 404s)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /login_step1.php"); 
    exit();
}

// 3. Global Variables
$ADMIN_ID = $_SESSION['login_user'] ?? '';
$userRole = $_SESSION['role'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SCS | Admin Dashboard</title>

  <link rel="stylesheet" href="/assests/css/admin.css" />
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=ADLaM+Display&family=Inter:wght@400;600&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <script>
    const userRole = "<?php echo $userRole; ?>";
  </script>

  <script src="/assests/js/app.js" defer></script>
</head>
<body>
  <div class="layout">