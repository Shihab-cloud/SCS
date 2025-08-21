<?php
session_start();
require_once __DIR__ . '/../db/config.php';  // Include DB connection

$student_id = $_SESSION['login_user'];  // Get student ID from session
$error = "";
$success = "";

// Get the student's current profile info
$stmt = $conn->prepare("SELECT first_name, last_name, email FROM Students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update on form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_email = $_POST['email'];
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];

    // Update the profile information
    $update_stmt = $conn->prepare("UPDATE Students SET first_name = ?, last_name = ?, email = ? WHERE student_id = ?");
    $update_stmt->bind_param("ssss", $new_first_name, $new_last_name, $new_email, $student_id);

    if ($update_stmt->execute()) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Failed to update profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Profile | SCS</title>
    <link rel="stylesheet" href="/smart_cloud_system/assests/css/student.css" />
</head>
<body>
  <div class="layout">
    <?php include __DIR__ . '/../includes/sidebar_student.php'; ?>
    
    <main class="content">
      <div class="card">
        <h2>Profile</h2>
        <form method="POST">
            <label for="first_name">First Name</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required />

            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required />

            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required />

            <button type="submit">Update Profile</button>
        </form>

        <?php if ($error): ?>
          <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($success): ?>
          <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>
