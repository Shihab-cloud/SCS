<?php
require_once __DIR__ . '/../includes/header_student.php';
include __DIR__ . '/../includes/sidebar_student.php';
require_once __DIR__ . '/../db/config.php';

// Fetch notices related to student courses
$student_id = $_SESSION['login_user'];

// Get the courses the student is enrolled in
$courses_query = $conn->prepare("
  SELECT course_id FROM Enrollments WHERE student_id = ?
");
$courses_query->bind_param('s', $student_id);
$courses_query->execute();
$courses_result = $courses_query->get_result();

// Create a list of course IDs for query
$course_ids = [];
while ($course = $courses_result->fetch_assoc()) {
    $course_ids[] = $course['course_id'];
}

$course_ids_str = implode("','", $course_ids);  // for SQL IN clause

// Get notices related to student courses
$notices_query = $conn->prepare("
  SELECT title, description, posted_date, target_audience
  FROM Notices
  WHERE target_audience = 'ALL' OR target_audience IN ('$course_ids_str')
  ORDER BY posted_date DESC
");
$notices_query->execute();
$notices_result = $notices_query->get_result();
?>

<div class="card">
  <h2>Notices</h2>
  <table class="table">
    <tr><th>Title</th><th>Date</th><th>Target</th></tr>
    <?php while ($notice = $notices_result->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($notice['title']); ?></td>
        <td><?php echo htmlspecialchars($notice['posted_date']); ?></td>
        <td><?php echo htmlspecialchars($notice['target_audience']); ?></td> <!-- Display the target audience -->
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<input type="text" id="searchNotice" placeholder="Search Notices" onkeyup="filterNotices()" />
<script>
  function filterNotices() {
    const input = document.getElementById('searchNotice').value.toLowerCase();
    const table = document.querySelector('.table');
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
      const title = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
      if (title.includes(input)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
</script>