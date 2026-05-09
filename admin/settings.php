<?php
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

$username = $_SESSION['login_user'];  // Admin Username (stored in session)

// Check if the session is set correctly
if (!$username) {
    die("Admin username is missing from session. Please log in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $current_password = $_POST['current_password'];

    // Validate current password using username instead of admin_id
    $stmt = $conn->prepare("SELECT password FROM Admins WHERE username = ?");
    $stmt->bind_param("s", $username);  // Use "s" for string parameter (for username)
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Check if current password matches the stored password
        if ($admin['password'] === $current_password) {
            // Update password if the current password is correct
            $update_stmt = $conn->prepare("UPDATE Admins SET password = ? WHERE username = ?");
            $update_stmt->bind_param("ss", $new_password, $username); // "ss" for two strings
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                $success_message = "Password updated successfully!";
            } else {
                $error_message = "Error updating password. Please try again.";
            }
        } else {
            $error_message = "Current password is incorrect.";
        }
    } else {
        $error_message = "Admin not found.";
    }
}
?>

<div class="card">
  <h2>Admin Profile & Settings</h2>
  <form method="POST">
    <label for="current_password">Current Password</label>
    <input type="password" name="current_password" required>

    <label for="new_password">New Password</label>
    <input type="password" name="new_password" required>

    <button type="submit">Change Password</button>
  </form>

  <?php if (isset($error_message)): ?>
    <p class="error"><?php echo $error_message; ?></p>
  <?php endif; ?>

  <?php if (isset($success_message)): ?>
    <p class="success"><?php echo $success_message; ?></p>
  <?php endif; ?>
</div>

</main></div></body></html>