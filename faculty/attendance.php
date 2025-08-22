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

$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $date = $_POST['date'] ?? date('Y-m-d');
  foreach($_POST['status'] ?? [] as $sid => $val) {
    $st = strtoupper($val)==='P' ? 'P' : 'A';
    $ins = $conn->prepare(" INSERT INTO Attendance (student_id, course_id, date, status)
                            VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE status=VALUES(status)");
    $ins->bind_param('ssss', $sid, $course_id, $date, $st);
    $ins->execute();
  }
  $msg = 'Attendance saved.';
}

$roster = $conn->prepare(" SELECT s.student_id, s.first_name, s.last_name
                           FROM Enrollments e
                           JOIN Students s ON s.student_id = e.student_id
                           WHERE e.course_id = ?
                           ORDER BY s.last_name, s.first_name");

$roster->bind_param('s', $course_id);
$roster->execute();
$list = $roster->get_result();
?>
<div class="card">
  <h2>Attendance — <?php echo htmlspecialchars($course_id); ?></h2>
  <form method="post">
    <div class="row">
      <div class="col-4">
        <label>Date</label>
        <input class="input" type="date" name="date" value="<?php echo date('Y-m-d'); ?>">
      </div>
      <div class="col-4 helper" style="display:flex; align-items:end;">
        <button class="btn btn-outline" type="button" data-action="mark-all-present">Mark All Present</button>
      </div>
      <div class="col-4 helper" style="display:flex; align-items:end;">
        <button class="btn btn-outline" type="button" data-action="mark-all-absent">Mark All Absent</button>
      </div>
    </div>
    <table class="table" data-table="attendance">
      <tr><th>Student</th><th>Mark</th></tr>
      <?php while($s = $list->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars($s['student_id'].' — '.$s['first_name'].' '.$s['last_name']); ?></td>
        <td>
          <select name="status[<?php echo htmlspecialchars($s['student_id']); ?>]" class="input">
            <option value="P">Present</option>
            <option value="A">Absent</option>
          </select>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
    <div class="right"><button class="btn" type="submit">Save Attendance</button></div>
    <?php if($msg): ?><div class="helper"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
  </form>
</div>
</main></div></body></html>