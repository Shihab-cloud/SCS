<?php
require_once __DIR__ . '/../includes/header_student.php';
include __DIR__ . '/../includes/sidebar_student.php';
require_once __DIR__ . '/../db/config.php';

$student_id = $_SESSION['login_user'];  // Logged-in student ID

// Get the grades for each course
$stmt = $conn->prepare("SELECT c.course_id, c.course_name, r.marks_obtained, r.grade FROM Results r JOIN Courses c ON c.course_id = r.course_id WHERE r.student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$grades_result = $stmt->get_result();
?>

<div class="card">
  <h2>My Grades</h2>
  <table class="table">
    <tr><th>Course</th><th>Marks</th><th>Grade</th></tr>
    <?php while ($grades = $grades_result->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($grades['course_id']); ?></td>
      <td><?php echo htmlspecialchars($grades['marks_obtained']); ?></td>
      <td><?php echo htmlspecialchars($grades['grade']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</main></div></body></html>