<?php
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // Fetch the student details
    $stmt = $conn->prepare("SELECT * FROM Students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
}

// Update student details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE Students SET first_name = ?, last_name = ?, email = ? WHERE student_id = ?");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $student_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Student updated successfully!";
        header("Location: student.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating student.";
    }
}
?>

<div class="card">
  <h2>Edit Student</h2>
  <form action="" method="POST">
    <div class="form-group">
      <label for="first_name">First Name:</label>
      <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
    </div>
    <div class="form-group">
      <label for="last_name">Last Name:</label>
      <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
    </div>
    <div class="form-group">
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
    </div>
    <button type="submit" class="btn">Update Student</button>
  </form>
</div>

</main></div></body></html>