<?php
require_once __DIR__ . '/../includes/header_student.php';
include __DIR__ . '/../includes/sidebar_student.php';
require_once __DIR__ . '/../db/config.php';

$student_id = $_SESSION['login_user'];  // Logged-in student ID

// Fetch enrolled courses and their corresponding faculty information
$stmt = $conn->prepare("
  SELECT c.course_id, c.course_name, f.first_name AS instructor
  FROM Enrollments e
  JOIN Courses c ON c.course_id = e.course_id
  JOIN Faculty_Courses fc ON fc.course_id = c.course_id
  JOIN Faculty f ON f.faculty_id = fc.faculty_id
  WHERE e.student_id = ?
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$courses_result = $stmt->get_result();
?>

<div class="card">
  <h2>My Courses</h2>
  <table class="table">
    <tr><th>Course</th><th>Name</th><th>Instructor</th></tr>
    <?php while ($course = $courses_result->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($course['course_id']); ?></td>
      <td><?php echo htmlspecialchars($course['course_name']); ?></td>
      <td><?php echo htmlspecialchars($course['instructor']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</main></div></body></html>