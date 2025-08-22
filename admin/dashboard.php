<?php
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

// Fetch statistics
$stmt = $conn->prepare("SELECT COUNT(*) AS total_students FROM Students");
$stmt->execute();
$students_count = $stmt->get_result()->fetch_assoc()['total_students'];

$stmt = $conn->prepare("SELECT COUNT(*) AS total_faculty FROM Faculty");
$stmt->execute();
$faculty_count = $stmt->get_result()->fetch_assoc()['total_faculty'];

$stmt = $conn->prepare("SELECT COUNT(*) AS total_courses FROM Courses");
$stmt->execute();
$courses_count = $stmt->get_result()->fetch_assoc()['total_courses'];

$stmt = $conn->prepare("SELECT title FROM Notices ORDER BY posted_date DESC LIMIT 5");
$stmt->execute();
$notices_result = $stmt->get_result();
?>

<div class="card">
  <h2>Welcome to the Admin Dashboard</h2>
  <p>Total Students: <?php echo $students_count; ?></p>
  <p>Total Faculty: <?php echo $faculty_count; ?></p>
  <p>Total Courses: <?php echo $courses_count; ?></p>
</div>

<div class="card">
  <h2>Recent Notices</h2>
  <ul>
    <?php while ($notice = $notices_result->fetch_assoc()): ?>
    <li><?php echo htmlspecialchars($notice['title']); ?></li>
    <?php endwhile; ?>
  </ul>
</div>

</main>
</div>
</body>
</html>