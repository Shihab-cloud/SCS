<?php
require_once __DIR__ . '/../includes/header_student.php';
include __DIR__ . '/../includes/sidebar_student.php';
require_once __DIR__ . '/../db/config.php';

$student_id = $_SESSION['login_user'];  // Get student ID from session
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];
    $current_password = $_POST['current_password'];

    // Check if current password is correct
    $stmt = $conn->prepare("SELECT password FROM Students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($current_password, $user['password'])) {
        // Update email and password
        $stmt = $conn->prepare("UPDATE Students SET email = ?, password = ? WHERE student_id = ?");
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $new_email, $hashed_password, $student_id);
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile.";
        }
    } else {
        $error = "Incorrect current password.";
    }
}
?>

<form method="POST">
  <label for="email">Email</label>
  <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required />

  <label for="password">New Password</label>
  <input type="password" name="password" placeholder="Enter new password" />

  <label for="current_password">Current Password</label>
  <input type="password" name="current_password" placeholder="Enter current password" required />

  <button type="submit">Save Changes</button>
</form>

<?php if ($error): ?>
  <p class="error"><?php echo $error; ?></p>
<?php endif; ?>
<?php if (isset($success)): ?>
  <p class="success"><?php echo $success; ?></p>
<?php endif; ?>