<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../db/config.php';
include __DIR__ . '/../includes/sidebar_faculty.php';

$fid = $FACULTY_ID;
$msg = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $new = $_POST['new_password'] ?? '';
  if ($new) {
    $u = $conn->prepare("UPDATE Faculty SET password=? WHERE faculty_id=?");
    $u->bind_param('ss', $new, $fid);
    $u->execute();
    $msg = 'Password updated.';
  }
}
?>
<div class="card">
  <h2>Settings</h2>
  <form method="post" class="col-6">
    <label>New Password</label>
    <input class="input" type="password" name="new_password" required>
    <button class="btn">Save</button>
    <?php if($msg): ?><div class="helper"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
  </form>
</div>
</main></div></body></html>