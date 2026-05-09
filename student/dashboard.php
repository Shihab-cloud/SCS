<?php
require_once __DIR__ . '/../includes/header_student.php';
include __DIR__ . '/../includes/sidebar_student.php';
require_once __DIR__ . '/../db/config.php';

$student_id = $_SESSION['login_user'];

// Get the student's full name
$stmt = $conn->prepare("SELECT first_name, last_name FROM Students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();

// Get recent notices for student
$notices = $conn->prepare("SELECT title, posted_date FROM Notices WHERE target_audience = 'ALL' OR target_audience LIKE ? ORDER BY posted_date DESC LIMIT 5");
$notices->bind_param('s', $student_id);
$notices->execute();
$notices_result = $notices->get_result();
?>

<div class="card">
  <h2>Welcome <?php echo htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']); ?>!</h2>
</div>

<div class="card">
  <h2>Recent Notices</h2>
  <table class="table">
    <tr><th>Title</th><th>Date</th></tr>
    <?php while ($notice = $notices_result->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($notice['title']); ?></td>
      <td><?php echo htmlspecialchars($notice['posted_date']); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</main></div></body></html>