<?php
// Do all processing BEFORE any HTML output.
//session_start();
require_once __DIR__ . '/../db/config.php';

// Uncomment while debugging DB issues
// mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$add_vals = ['course_id'=>'', 'course_name'=>'', 'description'=>'', 'credits'=>''];

/* ------------ ADD COURSE ------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'add_course') {
    $course_id   = trim($_POST['course_id'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $credits_raw = $_POST['credits'] ?? '';

    // keep sticky values if we need to re-render
    $add_vals = ['course_id'=>$course_id, 'course_name'=>$course_name, 'description'=>$description, 'credits'=>$credits_raw];

    $errors = [];
    if ($course_id === '')   $errors[] = "Course ID is required.";
    if ($course_name === '') $errors[] = "Course name is required.";
    if ($credits_raw === '' || !is_numeric($credits_raw)) $errors[] = "Credits must be a number.";
    $credits = (int)$credits_raw;

    if (!$errors) {
        $stmt = $conn->prepare(
            "INSERT INTO courses (course_id, course_name, description, credits)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("sssi", $course_id, $course_name, $description, $credits);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Course “{$course_id}” added.";
            header("Location: ".$_SERVER['PHP_SELF']); // PRG on success
            exit;
        } else {
            $_SESSION['error'] = "Could not add course. (Possible duplicate ID or DB error.)";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = implode(' ', $errors);
    }
}

/* ------------ DELETE (you said works) ------------ */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $d = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
        $d->bind_param("s", $id);
        $d->execute();
        $_SESSION['message'] = $d->affected_rows ? "Course “{$id}” deleted." : "Course not found.";
        $d->close();
    } catch (mysqli_sql_exception $e) {
        $_SESSION['error'] = "Cannot delete course; it is referenced elsewhere.";
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

/* ------------ FETCH LIST ------------ */
$list = $conn->prepare("SELECT course_id, course_name, description, credits FROM courses ORDER BY course_id ASC");
$list->execute();
$courses = $list->get_result();

/* ------------ HTML OUTPUT ------------ */
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="card">
    <h2>Manage Courses</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <p style="color:green;margin:8px 0;"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;margin:8px 0;"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <h3>Add New Course</h3>
    <form method="POST" action="">
        <input type="hidden" name="form" value="add_course">
        <div class="form-group">
            <label for="course_id">Course ID</label>
            <input id="course_id" name="course_id" type="text" required
                   value="<?php echo htmlspecialchars($add_vals['course_id']); ?>">
        </div>
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input id="course_name" name="course_name" type="text" required
                   value="<?php echo htmlspecialchars($add_vals['course_name']); ?>">
        </div>
        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="2"><?php
                echo htmlspecialchars($add_vals['description']);
            ?></textarea>
        </div>
        <div class="form-group">
            <label for="credits">Credits</label>
            <input id="credits" name="credits" type="number" min="1" max="10" required
                   value="<?php echo htmlspecialchars($add_vals['credits']); ?>">
        </div>
        <button type="submit" class="btn">Add Course</button>
    </form>

    <hr style="margin:18px 0; border:0; border-top:1px solid #eee;">

    <table class="table">
        <tr>
            <th>Course ID</th>
            <th>Course Name</th>
            <th>Credits</th>
            <th>Action</th>
        </tr>
        <?php while ($c = $courses->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($c['course_id']); ?></td>
                <td title="<?php echo htmlspecialchars($c['description'] ?? ''); ?>">
                    <?php echo htmlspecialchars($c['course_name']); ?>
                </td>
                <td><?php echo (int)$c['credits']; ?></td>
                <td>
                    <a href="edit_course.php?id=<?php echo urlencode($c['course_id']); ?>">Edit</a> |
                    <a href="course.php?delete=<?php echo urlencode($c['course_id']); ?>"
                       onclick="return confirm('Delete course <?php echo htmlspecialchars($c['course_id']); ?>?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</main></div></body></html>