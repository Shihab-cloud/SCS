<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../db/config.php';
include __DIR__ . '/../includes/sidebar_faculty.php';

$fid = $FACULTY_ID;
$msg = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $title = trim($_POST['title'] ?? '');
  $desc  = trim($_POST['description'] ?? '');
  $target= trim($_POST['target_audience'] ?? '');
  if ($title && $desc) {
    $ins = $conn->prepare("INSERT INTO Notices (title, description, posted_by, posted_date, target_audience) VALUES (?, ?, ?, CURDATE(), ?)");
    $ins->bind_param('ssss', $title, $desc, $fid, $target);
    $ins->execute();
    $msg = 'Notice posted.';
  }
}
$all = $conn->prepare("SELECT title, posted_date, target_audience FROM Notices WHERE posted_by=? ORDER BY posted_date DESC");
$all->bind_param('s',$fid); $all->execute(); $rows = $all->get_result();
?>
<div class="card">
  <h2>Post Notice</h2>
  <form method="post">
    <label>Title</label><input class="input" name="title" required>
    <label>Target (course/section or ALL)</label><input class="input" name="target_audience" placeholder="e.g., CSE-311">
    <label>Description</label><textarea class="input" name="description" rows="5" required></textarea>
    <button class="btn">Publish</button>
    <?php if($msg): ?><div class="helper"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
  </form>
</div>

<div class="card">
  <h2>My Notices</h2>
  <table class="table">
    <tr><th>Title</th><th>Date</th><th>Target</th></tr>
    <?php while($n = $rows->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($n['title']); ?></td>
      <td><?php echo htmlspecialchars($n['posted_date']); ?></td>
      <td><?php echo htmlspecialchars($n['target_audience']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</main></div></body></html>