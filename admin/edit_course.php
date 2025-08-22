<?php
require_once __DIR__ . '/../db/config.php';

// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$original_id = $_GET['id'] ?? '';
if ($original_id === '') {
    $_SESSION['error'] = "Missing course id.";
    header("Location: course.php"); exit;
}

/* Load existing row */
$sel = $conn->prepare("SELECT course_id, course_name, description, credits FROM courses WHERE course_id = ?");
$sel->bind_param("s", $original_id);
$sel->execute();
$course = $sel->get_result()->fetch_assoc();
$sel->close();

if (!$course) {
    $_SESSION['error'] = "Course not found.";
    header("Location: course.php"); exit;
}

/* Update */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_course'])) {
    // Keep course_id immutable to avoid FK problems
    $course_id   = $_POST['course_id']; // hidden, same as original
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $credits     = isset($_POST['credits']) ? (int)$_POST['credits'] : null;

    if ($course_name !== '' && $credits !== null) {
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
        $_SESSION['error'] = "Course name and credits are required.";
    }
    header("Location: ".$_SERVER['REQUEST_URI']); exit;
}

/* Output HTML AFTER logic */
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="card">
    <h2>Edit Course</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['course_id']); ?>">
        <div class="form-group">
            <label>Course ID</label>
            <input type="text" value="<?php echo htmlspecialchars($course['course_id']); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input id="course_name" name="course_name" type="text"
                   value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="3"><?php
                echo htmlspecialchars($course['description'] ?? '');
            ?></textarea>
        </div>
        <div class="form-group">
            <label for="credits">Credits</label>
            <input id="credits" name="credits" type="number" min="1" max="10"
                   value="<?php echo (int)$course['credits']; ?>" required>
        </div>
        <button type="submit" name="update_course" class="btn">Save Changes</button>
        <a href="course.php" class="btn" style="margin-left:8px;">Cancel</a>
    </form>
</div>
</main></div></body></html>