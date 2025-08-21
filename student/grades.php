<?php
require_once __DIR__ . '/../includes/header_student.php';
include __DIR__ . '/../includes/sidebar_student.php';
require_once __DIR__ . '/../db/config.php';

$student_id = $_SESSION['login_user'];  // Logged-in student ID

// Get grades
$stmt = $conn->prepare("
  SELECT c.course_id, c.course_name, r.marks_obtained, r.grade
  FROM Results r
  JOIN Courses c ON c.course_id = r.course_id
  WHERE r.student_id = ?
  ORDER BY c.course_id
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$grades_result = $stmt->get_result();
?>

<div class="card">
  <h2>My Grades</h2>
  <table class="table">
    <tr><th>Course</th><th>Name</th><th>Marks</th><th>Grade</th></tr>
    <?php while ($row = $grades_result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['course_id']) ?></td>
        <td><?= htmlspecialchars($row['course_name']) ?></td>
        <td><?= htmlspecialchars($row['marks_obtained']) ?></td>
        <td><?= htmlspecialchars($row['grade']) ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>

<!-- Big full-width Download button -->
<div class="card">
  <div class="btn-row">
    <a href="grades_pdf.php" class="btn btn-lg">Download Grade</a>
  </div>
</div>

</main></div></body></html>