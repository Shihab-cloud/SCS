<?php
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

// Fetch all students
$stmt = $conn->prepare("SELECT * FROM Students");
$stmt->execute();
$students_result = $stmt->get_result();
?>

<div class="card">
  <h2>Manage Students</h2>
  <table class="table">
    <tr>
      <th>Student ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Action</th>
    </tr>
    <?php while ($student = $students_result->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($student['student_id']); ?></td>
      <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
      <td><?php echo htmlspecialchars($student['email']); ?></td>
      <td>
        <a href="edit_student.php?id=<?php echo $student['student_id']; ?>">Edit</a> |
        <a href="delete_student.php?id=<?php echo $student['student_id']; ?>" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</main></div></body></html>