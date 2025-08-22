<?php
require_once __DIR__ . '/../includes/header_student.php';
include __DIR__ . '/../includes/sidebar_student.php';
require_once __DIR__ . '/../db/config.php';

$student_id = $_SESSION['login_user'] ?? '';
if ($student_id === '') {
    die('Not logged in.');
}

/*
  Only schedules for courses the student is enrolled in.
  (If a course has multiple meeting times, they’ll all show.)
*/
$sql = "
SELECT
    cs.course_id,
    c.course_name,
    CONCAT(f.first_name, ' ', f.last_name) AS instructor,
    cs.day,
    DATE_FORMAT(cs.start_time, '%H:%i') AS start_time,
    DATE_FORMAT(cs.end_time,   '%H:%i') AS end_time,
    cs.room_number
FROM class_schedules cs
JOIN courses c      ON c.course_id  = cs.course_id
LEFT JOIN faculty f ON f.faculty_id = cs.faculty_id
WHERE EXISTS (
    SELECT 1
    FROM enrollments e
    WHERE e.student_id = ?
      AND e.course_id  = cs.course_id
)
ORDER BY c.course_name, cs.day, cs.start_time
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$schedules = $stmt->get_result();
?>

<div class="card">
  <h2>My Courses</h2>
  <table class="table">
    <tr>
      <th>Course</th>
      <th>Name</th>
      <th>Instructor</th>
      <th>Day</th>
      <th>Time</th>
      <th>Room</th>
    </tr>

    <?php if ($schedules && $schedules->num_rows): ?>
      <?php while ($row = $schedules->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['course_id']); ?></td>
          <td><?php echo htmlspecialchars($row['course_name']); ?></td>
          <td><?php echo htmlspecialchars($row['instructor'] ?? '—'); ?></td>
          <td><?php echo htmlspecialchars($row['day']); ?></td>
          <td><?php echo htmlspecialchars($row['start_time'].' – '.$row['end_time']); ?></td>
          <td><?php echo htmlspecialchars($row['room_number']); ?></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="6">No schedules found for your enrollments.</td></tr>
    <?php endif; ?>
  </table>
</div>

</main></div></body></html>