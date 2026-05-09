<?php
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

// Display all notices
$stmt = $conn->prepare("SELECT * FROM Notices");
$stmt->execute();
$notices_result = $stmt->get_result();
?>

<div class="card">
  <h2>Manage Notices</h2>
  <table class="table">
    <tr><th>Title</th><th>Description</th><th>Action</th></tr>
    <?php while ($notice = $notices_result->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($notice['title']); ?></td>
      <td><?php echo htmlspecialchars($notice['description']); ?></td>
      <td>
        <a href="edit_notice.php?id=<?php echo $notice['notice_id']; ?>">Edit</a> |
        <a href="delete_notice.php?id=<?php echo $notice['notice_id']; ?>">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</main></div></body></html>