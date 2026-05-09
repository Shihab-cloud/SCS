<?php
require_once __DIR__ . '/../includes/header_student.php';
include __DIR__ . '/../includes/sidebar_student.php';
require_once __DIR__ . '/../db/config.php';

$student_id = $_SESSION['login_user'];

//Retreives attendance record
$stmt = $conn->prepare("SELECT c.course_id, c.course_name, a.date, a.status FROM Attendance a JOIN Courses c ON c.course_id = a.course_id WHERE a.student_id = ? ORDER BY a.date DESC");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$attendance_result = $stmt->get_result();
?>

<div class="card">
  <h2>Attendance</h2>
  <table class="table">
    <tr><th>Course</th><th>Date</th><th>Status</th></tr>
    <?php while ($attendance = $attendance_result->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($attendance['course_id']); ?></td>
      <td><?php echo htmlspecialchars($attendance['date']); ?></td>
      <td><?php echo htmlspecialchars($attendance['status']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</main></div></body></html>