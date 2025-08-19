<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../db/config.php';
include __DIR__ . '/../includes/sidebar_faculty.php';

$fid = $FACULTY_ID;
$stmt = $conn->prepare("
  SELECT cs.course_id, c.course_name, cs.day, cs.start_time, cs.end_time, cs.room_number
  FROM Class_Schedules cs
  JOIN Courses c ON c.course_id = cs.course_id
  WHERE cs.faculty_id = ?
  ORDER BY FIELD(cs.day,'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'), cs.start_time
");
$stmt->bind_param('s', $fid);
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="card">
  <h2>Weekly Schedule</h2>
  <table class="table">
    <tr><th>Day</th><th>Course</th><th>Name</th><th>Time</th><th>Room</th></tr>
    <?php while($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['day']); ?></td>
      <td><?php echo htmlspecialchars($r['course_id']); ?></td>
      <td><?php echo htmlspecialchars($r['course_name']); ?></td>
      <td><?php echo substr($r['start_time'],0,5).'–'.substr($r['end_time'],0,5); ?></td>
      <td><?php echo htmlspecialchars($r['room_number']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</main></div></body></html>