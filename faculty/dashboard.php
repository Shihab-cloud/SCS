<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../db/config.php';
include __DIR__ . '/../includes/sidebar_faculty.php';

$today = date('l'); //Day
$fid = $FACULTY_ID;

// Today’s classes
$cls = $conn->prepare(" SELECT cs.course_id, c.course_name, cs.day, cs.start_time, cs.end_time, cs.room_number
                        FROM Class_Schedules cs JOIN Courses c ON c.course_id = cs.course_id
                        WHERE cs.faculty_id = ? AND cs.day = ?
                        ORDER BY cs.start_time");
$cls->bind_param('ss', $fid, $today);
$cls->execute();
$classes = $cls->get_result();

// Recent notices
$nts = $conn->prepare(" SELECT title, posted_date, target_audience FROM Notices WHERE posted_by = ? ORDER BY posted_date DESC LIMIT 5");
$nts->bind_param('s', $fid);
$nts->execute();
$notices = $nts->get_result();
?>
<div class="card">
  <h2>Today’s Classes (<?php echo htmlspecialchars($today); ?>)</h2>
  <table class="table">
    <tr><th>Course</th><th>Name</th><th>Time</th><th>Room</th></tr>
    <?php while($r = $classes->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['course_id']); ?></td>
      <td><?php echo htmlspecialchars($r['course_name']); ?></td>
      <td><?php echo substr($r['start_time'],0,5).'–'.substr($r['end_time'],0,5); ?></td>
      <td><?php echo htmlspecialchars($r['room_number']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<div class="card">
  <h2>Recent Notices</h2>
  <table class="table">
    <tr><th>Title</th><th>Date</th><th>Target</th></tr>
    <?php while($n = $notices->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($n['title']); ?></td>
      <td><?php echo htmlspecialchars($n['posted_date']); ?></td>
      <td><?php echo htmlspecialchars($n['target_audience']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</main></div></body></html>