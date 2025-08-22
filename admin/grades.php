<?php
// Process BEFORE any HTML
//if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db/config.php';
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$add_vals = ['student_id'=>'','course_id'=>'','marks'=>'','grade'=>''];

/* ---------- ADD GRADE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'add_grade') {
    $student_id = trim($_POST['student_id'] ?? '');
    $course_id  = trim($_POST['course_id'] ?? '');
    $marks_raw  = $_POST['marks'] ?? '';
    $grade      = trim($_POST['grade'] ?? '');

    $add_vals = ['student_id'=>$student_id,'course_id'=>$course_id,'marks'=>$marks_raw,'grade'=>$grade];

    $errors = [];
    if ($student_id === '') $errors[] = "Student is required.";
    if ($course_id  === '') $errors[] = "Course is required.";
    if ($marks_raw === '' || !is_numeric($marks_raw)) $errors[] = "Marks must be a number.";
    if ($grade === '') $errors[] = "Grade is required.";
    $marks = (float)$marks_raw;

    if (!$errors) {
        // check duplicate (composite key student_id+course_id)
        $chk = $conn->prepare("SELECT 1 FROM results WHERE student_id=? AND course_id=?");
        $chk->bind_param("ss", $student_id, $course_id);
        $chk->execute();
        $exists = $chk->get_result()->num_rows > 0;
        $chk->close();

        if ($exists) {
            $_SESSION['error'] = "Grade already exists for this student & course. Use Edit.";
        } else {
            $ins = $conn->prepare(
                "INSERT INTO results (student_id, course_id, marks_obtained, grade)
                 VALUES (?,?,?,?)"
            );
            $ins->bind_param("ssds", $student_id, $course_id, $marks, $grade);
            if ($ins->execute()) {
                $_SESSION['message'] = "Grade saved.";
                header("Location: ".$_SERVER['PHP_SELF']); exit;
            }
            $_SESSION['error'] = "Could not save grade.";
        }
    } else {
        $_SESSION['error'] = implode(' ', $errors);
    }
}

/* ---------- FETCH DATA FOR UI ---------- */
$students = $conn->query("SELECT student_id, first_name, last_name FROM students ORDER BY first_name, last_name")->fetch_all(MYSQLI_ASSOC);
$courses  = $conn->query("SELECT course_id, course_name FROM courses ORDER BY course_name")->fetch_all(MYSQLI_ASSOC);
$grades   = $conn->query("SELECT r.student_id, r.course_id, s.first_name, s.last_name, c.course_name, r.marks_obtained, r.grade
                          FROM results r
                          JOIN students s ON r.student_id = s.student_id
                          JOIN courses  c ON r.course_id  = c.course_id
                          ORDER BY s.last_name, s.first_name, c.course_name");

require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="card">
    <h2>Manage Grades</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <p style="color:green"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <h3>Add New Grade</h3>
    <form method="POST" action="">
        <input type="hidden" name="form" value="add_grade">
        <label>Student:</label>
        <select name="student_id" required>
            <option value="">Select Student</option>
            <?php foreach ($students as $s): ?>
                <option value="<?php echo $s['student_id']; ?>"
                    <?php if ($add_vals['student_id']===$s['student_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($s['first_name'].' '.$s['last_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Course:</label>
        <select name="course_id" required>
            <option value="">Select Course</option>
            <?php foreach ($courses as $c): ?>
                <option value="<?php echo $c['course_id']; ?>"
                    <?php if ($add_vals['course_id']===$c['course_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($c['course_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Marks:</label>
        <input type="number" step="0.01" name="marks" required
               value="<?php echo htmlspecialchars($add_vals['marks']); ?>">

        <label>Grade:</label>
        <input type="text" name="grade" required
               value="<?php echo htmlspecialchars($add_vals['grade']); ?>">

        <button type="submit" class="btn">Save Grade</button>
    </form>
</div>

<div class="card">
    <h3>All Grades</h3>
    <table class="table">
        <tr>
            <th>Student</th>
            <th>Course</th>
            <th>Marks</th>
            <th>Grade</th>
            <th>Action</th>
        </tr>
        <?php if ($grades && $grades->num_rows): while ($row = $grades->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                <td><?php echo htmlspecialchars($row['marks_obtained']); ?></td>
                <td><?php echo htmlspecialchars($row['grade']); ?></td>
                <td>
                    <a href="edit_grades.php?student_id=<?php echo urlencode($row['student_id']); ?>&course_id=<?php echo urlencode($row['course_id']); ?>">Edit</a> |
                    <a href="delete_grades.php?student_id=<?php echo urlencode($row['student_id']); ?>&course_id=<?php echo urlencode($row['course_id']); ?>"
                       onclick="return confirm('Delete this grade?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="5">No grades found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</main></div></body></html>