<?php
session_start();
require_once __DIR__ . '/../db/config.php';  // Include database connection

$student_id = $_SESSION['login_user']; // Get student ID from session
$error = "";
$success_message = "";

// Handle password change form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the new password matches the confirm password
    if ($new_password !== $confirm_password) {
        $error = "New password and confirmation do not match.";
    } else {
        // Check if current password is correct
        $stmt = $conn->prepare("SELECT password FROM Students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Compare entered current password with the stored one
        if ($user && $current_password === $user['password']) {
            // Update the password in the database (plain text)
            $update_stmt = $conn->prepare("UPDATE Students SET password = ? WHERE student_id = ?");
            $update_stmt->bind_param("ss", $new_password, $student_id);
            $update_stmt->execute();

            // Optional: Show a success message
            $success_message = "Password updated successfully!";
        } else {
            $error = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Settings | SCS</title>
  <link rel="stylesheet" href="/smart_cloud_system/assests/css/student.css" />
</head>
<body>
  <div class="layout">
    <?php include __DIR__ . '/../includes/sidebar_student.php'; ?>

    <main class="content">
      <div class="card">
        <h2>Settings</h2>

        <form method="POST">
          <label for="current_password">Current Password</label>
          <input type="password" name="current_password" placeholder="Enter your current password" required />

          <label for="new_password">New Password</label>
          <input type="password" name="new_password" placeholder="Enter a new password" required />

          <label for="confirm_password">Confirm New Password</label>
          <input type="password" name="confirm_password" placeholder="Confirm your new password" required />

          <button type="submit">Save</button>
        </form>

        <?php if ($error): ?>
          <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
          <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>