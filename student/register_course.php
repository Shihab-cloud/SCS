<?php
// ---------- bootstrap (run logic before any HTML) ----------
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db/config.php';

$student_id = $_SESSION['login_user'] ?? '';
if ($student_id === '') {
    die('Not logged in.');
}

function current_semester_label(): string {
    $y = (int)date('Y');
    $m = (int)date('n');
    if ($m >= 9)  return "Fall {$y}";
    if ($m >= 5)  return "Summer {$y}";
    return "Spring {$y}";
}

/* ------------------ REGISTER (POST) ------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'register') {
    $course_id = trim($_POST['course_id'] ?? '');
    $semester  = trim($_POST['semester'] ?? current_semester_label());

    if ($course_id === '') {
        $_SESSION['error'] = 'Please choose a course.';
    } else {
        // Check if already enrolled
        $chk = $conn->prepare("SELECT 1 FROM enrollments WHERE student_id = ? AND course_id = ?");
        $chk->bind_param("ss", $student_id, $course_id);
        $chk->execute();
        $exists = $chk->get_result()->num_rows > 0;
        $chk->close();

        if ($exists) {
            $_SESSION['error'] = 'You are already enrolled in this course.';
        } else {
            // Insert (grade left NULL)
            $ins = $conn->prepare("INSERT INTO enrollments (student_id, course_id, semester) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $student_id, $course_id, $semester);
            if ($ins->execute()) {
                $_SESSION['message'] = 'Course registered successfully.';
            } else {
                $_SESSION['error'] = 'Could not register for the course.';
            }
            $ins->close();
        }
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

/* ------------------ DROP (GET) ------------------ */
if (isset($_GET['drop'])) {
    $drop_course = $_GET['drop'];
    $del = $conn->prepare("DELETE FROM enrollments WHERE student_id = ? AND course_id = ?");
    $del->bind_param("ss", $student_id, $drop_course);
    $del->execute();
    $_SESSION['message'] = $del->affected_rows ? 'Course dropped.' : 'Course not found or already removed.';
    $del->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

/* ------------------ DATA FOR UI ------------------ */
// Courses NOT yet enrolled by this student (for the select)
$avail_q = $conn->prepare(" SELECT c.course_id, c.course_name
                            FROM courses c
                            WHERE NOT EXISTS (
                                  SELECT 1 FROM enrollments e
                                  WHERE e.student_id = ? AND e.course_id = c.course_id
                            )
                            ORDER BY c.course_name");

$avail_q->bind_param("s", $student_id);
$avail_q->execute();
$available_courses = $avail_q->get_result()->fetch_all(MYSQLI_ASSOC);
$avail_q->close();

//current courses (with instructor name if mapping exists)
$mine = $conn->prepare(" SELECT e.course_id, c.course_name, CONCAT(f.first_name, ' ', f.last_name) AS instructor, e.semester
                         FROM enrollments e
                         JOIN courses c ON c.course_id = e.course_id
                         LEFT JOIN faculty_courses fc ON fc.course_id = c.course_id
                         LEFT JOIN faculty f ON f.faculty_id = fc.faculty_id
                         WHERE e.student_id = ?
                         ORDER BY c.course_name");

$mine->bind_param("s", $student_id);
$mine->execute();
$my_courses = $mine->get_result();
$mine->close();

// Semester options
$y = (int)date('Y');
$semester_options = ["Spring {$y}", "Summer {$y}", "Fall {$y}"];
$default_semester = current_semester_label();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register Courses | SCS</title>
  <link rel="stylesheet" href="/smart_cloud_system/assests/css/student.css" />
</head>
<body>
  <div class="layout">
    <?php include __DIR__ . '/../includes/sidebar_student.php'; ?>

    <main class="content">
      <div class="card">
        <h2>Register for Courses</h2>

        <?php if (!empty($_SESSION['message'])): ?>
          <p style="color:green;"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
          <p style="color:red;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
          <input type="hidden" name="form" value="register">

          <label for="course_id">Course</label>
          <select name="course_id" id="course_id" required>
            <option value="">Select a course</option>
            <?php foreach ($available_courses as $c): ?>
              <option value="<?php echo htmlspecialchars($c['course_id']); ?>">
                <?php echo htmlspecialchars($c['course_id'].' — '.$c['course_name']); ?>
              </option>
            <?php endforeach; ?>
          </select>

          <label for="semester">Semester</label>
          <select name="semester" id="semester">
            <?php foreach ($semester_options as $opt): ?>
              <option value="<?php echo htmlspecialchars($opt); ?>"
                <?php if ($opt === $default_semester) echo 'selected'; ?>>
                <?php echo htmlspecialchars($opt); ?>
              </option>
            <?php endforeach; ?>
          </select>

          <button type="submit" class="btn">Register</button>
        </form>
      </div>

      <div class="card">
        <h2>My Courses</h2>
        <table class="table">
          <tr>
            <th>Course</th>
            <th>Name</th>
            <th>Instructor</th>
            <th>Semester</th>
            <th>Action</th>
          </tr>
          <?php if ($my_courses && $my_courses->num_rows): ?>
            <?php while ($row = $my_courses->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['course_id']); ?></td>
                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                <td><?php echo htmlspecialchars($row['instructor'] ?? '—'); ?></td>
                <td><?php echo htmlspecialchars($row['semester']); ?></td>
                <td>
                  <a href="?drop=<?php echo urlencode($row['course_id']); ?>"
                     onclick="return confirm('Drop <?php echo htmlspecialchars($row['course_id']); ?>?');">Drop</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
              <tr><td colspan="5">You are not registered in any courses yet.</td></tr>
          <?php endif; ?>
        </table>
      </div>
    </main>
  </div>
</body>
</html>