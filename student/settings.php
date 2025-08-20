<?php
require_once __DIR__ . '/../includes/header_student.php';
include __DIR__ . '/../includes/sidebar_student.php';
require_once __DIR__ . '/../db/config.php';

$student_id = $_SESSION['login_user']; // Get student ID from session

// Check if settings are posted (example: theme)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $theme = $_POST['theme'];  // light or dark
    $language = $_POST['language'];  // preferred language

    // Update preferences in the database
    $stmt = $conn->prepare("UPDATE Students SET theme = ?, language = ? WHERE student_id = ?");
    $stmt->bind_param("sss", $theme, $language, $student_id);
    $stmt->execute();
}
?>

<form method="POST">
  <label for="theme">Theme</label>
  <select name="theme">
    <option value="light" <?php if ($theme === 'light') echo 'selected'; ?>>Light</option>
    <option value="dark" <?php if ($theme === 'dark') echo 'selected'; ?>>Dark</option>
  </select>

  <label for="language">Language</label>
  <select name="language">
    <option value="en" <?php if ($language === 'en') echo 'selected'; ?>>English</option>
    <option value="fr" <?php if ($language === 'fr') echo 'selected'; ?>>French</option>
  </select>

  <button type="submit">Save Settings</button>
</form>