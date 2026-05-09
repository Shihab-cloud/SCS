<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../db/config.php';
include __DIR__ . '/../includes/sidebar_faculty.php';

$fid = $FACULTY_ID;
$stmt = $conn->prepare(" SELECT cs.course_id, c.course_name, cs.day, cs.start_time, cs.end_time, cs.room_number
                         FROM Class_Schedules cs JOIN Courses c ON c.course_id = cs.course_id
                         WHERE cs.faculty_id = ?
                         ORDER BY c.course_name");
$stmt->bind_param('s', $fid);
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="card">
  <h2>My Courses</h2>
  <table class="table">
    <tr><th>Course</th><th>Name</th><th>Schedule</th><th>Room</th><th>Actions</th></tr>
    <?php while($r = $res->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['course_id']); ?></td>
      <td><?php echo htmlspecialchars($r['course_name']); ?></td>
      <td><?php echo htmlspecialchars($r['day']).' '.substr($r['start_time'],0,5).'–'.substr($r['end_time'],0,5); ?></td>
      <td><?php echo htmlspecialchars($r['room_number']); ?></td>
      <td>
        <a class="btn btn-outline" href="roster.php?course_id=<?php echo urlencode($r['course_id']); ?>">Roster</a>
        <a class="btn btn-outline" href="attendance.php?course_id=<?php echo urlencode($r['course_id']); ?>">Attendance</a>
        <a class="btn btn-outline" href="gradebook.php?course_id=<?php echo urlencode($r['course_id']); ?>">Gradebook</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</main></div></body></html>