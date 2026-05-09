<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../db/config.php';
include __DIR__ . '/../includes/sidebar_faculty.php';

$fid = $FACULTY_ID;
$course_id = $_GET['course_id'] ?? '';
$semester  = $_GET['semester']  ?? 'Fall-2025';

if (!$course_id) { echo "<div class='card'>No course selected.</div></main></div></body></html>"; exit; }

$auth = $conn->prepare("SELECT 1 FROM Class_Schedules WHERE faculty_id=? AND course_id=?");
$auth->bind_param('ss', $fid, $course_id);
$auth->execute();
if ($auth->get_result()->num_rows === 0) { echo "<div class='card'>Unauthorized.</div></main></div></body></html>"; exit; }

$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  foreach($_POST['marks'] ?? [] as $sid => $m) {
    $marks = is_numeric($m) ? floatval($m) : null;
    $grade = $_POST['grade'][$sid] ?? '';
    if ($marks===null && $grade==='') continue;

    $ins = $conn->prepare(" INSERT INTO Results (student_id, course_id, semester, marks_obtained, grade)
                            VALUES (?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE marks_obtained=VALUES(marks_obtained), grade=VALUES(grade)");
    $ins->bind_param('sssds', $sid, $course_id, $semester, $marks, $grade);
    $ins->execute();
  }
  $msg = 'Grades saved.';
}

$roster = $conn->prepare(" SELECT s.student_id, s.first_name, s.last_name
                           FROM Enrollments e JOIN Students s ON s.student_id = e.student_id
                           WHERE e.course_id = ?
                           ORDER BY s.last_name, s.first_name");
$roster->bind_param('s', $course_id);
$roster->execute();
$list = $roster->get_result();

// preload existing
$grades = [];
$gq = $conn->prepare("SELECT student_id, marks_obtained, grade FROM Results WHERE course_id=? AND semester=?");
$gq->bind_param('ss', $course_id, $semester);
$gq->execute();
$grs = $gq->get_result();
while($g = $grs->fetch_assoc()){ $grades[$g['student_id']] = $g; }
?>
<div class="card">
  <h2>Gradebook — <?php echo htmlspecialchars($course_id.' ('.$semester.')'); ?></h2>
  <form method="get" class="row" style="margin-bottom:12px;">
    <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>">
    <div class="col-6">
      <label>Semester</label>
      <input class="input" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
    </div>
    <div class="col-3" style="display:flex;align-items:flex-end;">
      <button class="btn btn-outline">Change</button>
    </div>
  </form>

  <form method="post">
    <table class="table">
      <tr><th>Student</th><th>Marks</th><th>Grade</th></tr>
      <?php while($s = $list->fetch_assoc()): 
        $sid = $s['student_id'];
        $m = $grades[$sid]['marks_obtained'] ?? '';
        $g = $grades[$sid]['grade'] ?? '';
      ?>
      <tr>
        <td><?php echo htmlspecialchars($sid.' — '.$s['first_name'].' '.$s['last_name']); ?></td>
        <td><input class="input" type="number" step="0.01" name="marks[<?php echo htmlspecialchars($sid); ?>]" value="<?php echo htmlspecialchars($m); ?>"></td>
        <td><input class="input" name="grade[<?php echo htmlspecialchars($sid); ?>]" value="<?php echo htmlspecialchars($g); ?>"></td>
      </tr>
      <?php endwhile; ?>
    </table>
    <div class="right"><button class="btn" type="submit">Save Grades</button></div>
    <?php if($msg): ?><div class="helper"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
  </form>
</div>
</main></div></body></html>