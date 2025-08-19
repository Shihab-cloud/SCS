<?php
session_start();
include("db/config.php");

$username = $_SESSION['login_user'] ?? '';
$role = $_SESSION['role'] ?? '';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];

    if ($role === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM Admins WHERE username = ?");
    } elseif ($role === 'faculty') {
        $stmt = $conn->prepare("SELECT * FROM Faculty WHERE faculty_id = ?");
    } elseif ($role === 'student') {
        $stmt = $conn->prepare("SELECT * FROM Students WHERE student_id = ?");
    } else {
        $error = "Unknown user type.";
    }

    if (empty($error)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $APP_BASE = '/smart_cloud_system';

        if ($user && $password === $user['password']) {
            // redirect to portal
          if ($role === 'admin') {
            header("Location: {$APP_BASE}/admin_dashboard.php");
          } elseif ($role === 'faculty') {
            header("Location: {$APP_BASE}/faculty/dashboard.php");  // <-- new path
          } else { // student
            header("Location: {$APP_BASE}/student_dashboard.php");
          }
          exit();
        } else {
            $error = "Incorrect password.";
          }
    }
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

    <div class="login-body">
      <p class="welcome-msg">Welcome <span class="user-id">👤 <?php echo htmlspecialchars($username); ?></span></p>
      <form method="POST">
        <label>Password</label>
        <div class="input-group">
            <input type="password" name="password" placeholder="Please enter your password" required>
            <span class="icon">🔒</span>
        </div>


        <?php if ($error): ?>
          <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <button type="submit">Login</button>
      </form>

      <div class="footer-links">
        <a href="forgot_password.php">Forgot your password?</a>
      </div>
    </div>
  </div>
</body>
</html>
