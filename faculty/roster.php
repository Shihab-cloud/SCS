<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../db/config.php';
include __DIR__ . '/../includes/sidebar_faculty.php';

$fid = $FACULTY_ID;
$course_id = $_GET['course_id'] ?? '';
if (!$course_id) { echo "<div class='card'>No course selected.</div></main></div></body></html>"; exit; }

$auth = $conn->prepare("SELECT 1 FROM Class_Schedules WHERE faculty_id=? AND course_id=?");
$auth->bind_param('ss', $fid, $course_id);
$auth->execute();
if ($auth->get_result()->num_rows === 0) { echo "<div class='card'>Unauthorized.</div></main></div></body></html>"; exit; }

$stmt = $conn->prepare("
  SELECT s.student_id, s.first_name, s.last_name, s.email
  FROM Enrollments e
  JOIN Students s ON s.student_id = e.student_id
  WHERE e.course_id = ?
  ORDER BY s.last_name, s.first_name
");
$stmt->bind_param('s', $course_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="card">
  <h2>Roster — <?php echo htmlspecialchars($course_id); ?></h2>
  <table class="table">
    <tr><th>Student ID</th><th>Name</th><th>Email</th></tr>
    <?php while($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['student_id']); ?></td>
      <td><?php echo htmlspecialchars($r['first_name'].' '.$r['last_name']); ?></td>
      <td><?php echo htmlspecialchars($r['email']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</main></div></body></html>