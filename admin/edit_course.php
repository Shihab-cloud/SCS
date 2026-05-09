<?php
//session_start();
require_once __DIR__ . '/../db/config.php';

// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$course_id = $_GET['id'] ?? '';
if ($course_id === '') {
    $_SESSION['error'] = "Missing course id.";
    header("Location: course.php"); exit;
}

/* Load existing */
$sel = $conn->prepare("SELECT course_id, course_name, description, credits FROM courses WHERE course_id = ?");
$sel->bind_param("s", $course_id);
$sel->execute();
$existing = $sel->get_result()->fetch_assoc();
$sel->close();

if (!$existing) {
    $_SESSION['error'] = "Course not found.";
    header("Location: course.php"); exit;
}

// sticky values (default = DB)
$vals = $existing;

/* Update */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'update_course') {
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $credits_raw = $_POST['credits'] ?? '';

    $vals['course_name'] = $course_name;
    $vals['description'] = $description;
    $vals['credits']     = $credits_raw;

    $errors = [];
    if ($course_name === '') $errors[] = "Course name is required.";
    if ($credits_raw === '' || !is_numeric($credits_raw)) $errors[] = "Credits must be a number.";
    $credits = (int)$credits_raw;

    if (!$errors) {
        $upd = $conn->prepare(
            "UPDATE courses SET course_name = ?, description = ?, credits = ? WHERE course_id = ?"
        );
        $upd->bind_param("ssis", $course_name, $description, $credits, $course_id);
        if ($upd->execute()) {
            $_SESSION['message'] = "Course “{$course_id}” updated.";
            header("Location: course.php"); exit;
        } else {
            $_SESSION['error'] = "Could not update course.";
        }
        $upd->close();
    } else {
        $_SESSION['error'] = implode(' ', $errors);
        // fall through to re-render with sticky $vals
    }
}

/* HTML */
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="card">
    <h2>Edit Course</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="form" value="update_course">
        <div class="form-group">
            <label>Course ID</label>
            <input type="text" value="<?php echo htmlspecialchars($course_id); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input id="course_name" name="course_name" type="text" required
                   value="<?php echo htmlspecialchars($vals['course_name']); ?>">
        </div>
        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="3"><?php
                echo htmlspecialchars($vals['description'] ?? '');
            ?></textarea>
        </div>
        <div class="form-group">
            <label for="credits">Credits</label>
            <input id="credits" name="credits" type="number" min="1" max="10" required
                   value="<?php echo htmlspecialchars($vals['credits']); ?>">
        </div>
        <button type="submit" class="btn">Save Changes</button>
        <a href="course.php" class="btn" style="margin-left:8px;">Cancel</a>
    </form>
</div>
</main></div></body></html>