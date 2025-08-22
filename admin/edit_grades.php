<?php
//if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db/config.php';
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$student_id = $_GET['student_id'] ?? '';
$course_id  = $_GET['course_id']  ?? '';
if ($student_id === '' || $course_id === '') {
    $_SESSION['error'] = "Missing identifiers.";
    header("Location: grades.php"); exit;
}

/* Load row with names for display */
$q = $conn->prepare("SELECT r.student_id, r.course_id, r.marks_obtained, r.grade,
                            s.first_name, s.last_name, c.course_name
                     FROM results r
                     JOIN students s ON r.student_id = s.student_id
                     JOIN courses  c ON r.course_id  = c.course_id
                     WHERE r.student_id=? AND r.course_id=?");
$q->bind_param("ss", $student_id, $course_id);
$q->execute();
$row = $q->get_result()->fetch_assoc();
$q->close();

if (!$row) { $_SESSION['error'] = "Grade not found."; header("Location: grades.php"); exit; }

$vals = ['marks'=>$row['marks_obtained'],'grade'=>$row['grade']];

/* Update */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'update_grade') {
    $marks_raw = $_POST['marks'] ?? '';
    $grade     = trim($_POST['grade'] ?? '');

    $vals['marks'] = $marks_raw;
    $vals['grade'] = $grade;

    $errors = [];
    if ($marks_raw === '' || !is_numeric($marks_raw)) $errors[] = "Marks must be a number.";
    if ($grade === '') $errors[] = "Grade is required.";
    $marks = (float)$marks_raw;

    if (!$errors) {
        $u = $conn->prepare("UPDATE results SET marks_obtained=?, grade=? WHERE student_id=? AND course_id=?");
        $u->bind_param("dsss", $marks, $grade, $student_id, $course_id);
        if ($u->execute()) {
            $_SESSION['message'] = "Grade updated.";
            header("Location: grades.php"); exit;
        }
        $_SESSION['error'] = "Could not update grade.";
    } else {
        $_SESSION['error'] = implode(' ', $errors);
    }
    header("Location: ".$_SERVER['REQUEST_URI']); exit;
}

require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="card">
    <h2>Edit Grade</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <p>
        <strong>Student:</strong> <?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?><br>
        <strong>Course:</strong>  <?php echo htmlspecialchars($row['course_name']); ?>
    </p>

    <form method="POST" action="">
        <input type="hidden" name="form" value="update_grade">
        <label>Marks:</label>
        <input type="number" step="0.01" name="marks" required value="<?php echo htmlspecialchars($vals['marks']); ?>">
        <label>Grade:</label>
        <input type="text" name="grade" required value="<?php echo htmlspecialchars($vals['grade']); ?>">
        <button type="submit" class="btn">Save Changes</button>
        <a href="grades.php" class="btn" style="margin-left:8px;">Cancel</a>
    </form>
</div>
</main></div></body></html>