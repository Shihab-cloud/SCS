<?php
session_start();
include("db/config.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $_SESSION['login_user'] = $username;

    // Check admin
    $stmt = $conn->prepare("SELECT * FROM Admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $_SESSION['role'] = 'admin';
        header("Location: login_step2.php");
        exit();
    }

    // Check faculty
    $stmt = $conn->prepare("SELECT * FROM Faculty WHERE faculty_id = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $_SESSION['role'] = 'faculty';
        header("Location: login_step2.php");
        exit();
    }

    // Check student
    $stmt = $conn->prepare("SELECT * FROM Students WHERE student_id = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $_SESSION['role'] = 'student';
        header("Location: login_step2.php");
        exit();
    }

    $error = "Username not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | SCS</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <div class="login-box">
    <div class="login-header">
      <h1>SCS</h1>
    </div>

    <form method="POST">
      <label>Username</label>
      <div class="input-group">
        <input type="text" name="username" placeholder="Please enter your username" required>
        <span class="icon">👤</span>
      </div>
      <?php if ($error): ?>
        <p class="error"><?php echo $error; ?></p>
      <?php endif; ?>
      <button type="submit">Next</button>
    </form>

    <div class="footer-links">
      <span>Don't have an account? <a href="register.php">Create One</a></span>
    </div>
  </div>
</body>
</html>