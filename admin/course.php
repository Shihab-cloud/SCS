<?php
// Process before output
// Start session safely (your includes might do this too, but this is safe)
//if (session_status() === PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../db/config.php';

/* ---------------- DEBUG TOOLS ---------------- */
$DEBUG_MODE = (isset($_GET['debug']) && $_GET['debug'] === '1');

if ($DEBUG_MODE) {
    // Show errors in the browser during debug only
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
    // Turn on strict mysqli error reporting in debug
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    header('X-Debug-Mode: 1');
}

/**
 * Print and log debug info without breaking pages in production.
 */
function debug_dump($label, $data) {
    global $DEBUG_MODE;
    if (!$DEBUG_MODE) return;
    $dump = is_string($data) ? $data : print_r($data, true);
    // Send to PHP error log
    error_log("[DEBUG] $label: " . (is_string($data) ? $data : var_export($data, true)));
    // Echo to page (escaped) so you can see it
    echo '<pre style="background:#111;color:#0f0;padding:8px;border-radius:6px;white-space:pre-wrap;overflow:auto;">';
    echo htmlspecialchars("[DEBUG] $label: \n" . $dump, ENT_QUOTES, 'UTF-8');
    echo "</pre>\n";
}

/* ---------- ADD COURSE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {

    debug_dump('RAW $_POST', $_POST);

    $course_id   = trim($_POST['course_id'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $credits     = isset($_POST['credits']) ? (int)$_POST['credits'] : null;

    // Show exactly what we’re about to bind
    debug_dump('Add Course Params', [
        'course_id'   => $course_id,
        'course_name' => $course_name,
        'description' => $description,
        'credits'     => $credits,
        'types'       => 'sssi'
    ]);

    if ($course_id !== '' && $course_name !== '' && $credits !== null) {
        $sql = "INSERT INTO courses (course_id, course_name, description, credits)
                VALUES (?, ?, ?, ?)";
        debug_dump('Add Course SQL', $sql);

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $course_id, $course_name, $description, $credits);

            $ok = $stmt->execute();
            debug_dump('Execute Result', [
                'ok' => $ok,
                'insert_id' => $conn->insert_id ?? null,
                'affected_rows' => $stmt->affected_rows,
            ]);

            if ($ok) {
                $_SESSION['message'] = "Course “{$course_id}” added.";
            } else {
                // (With mysqli_report strict, errors would throw — but keep this for non-debug mode)
                $_SESSION['error'] = "Could not add course (maybe duplicate ID?).";
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            debug_dump('MySQL Exception (Add)', $e->getMessage());
            $_SESSION['error'] = "Database error while adding course.";
        }
    } else {
        $_SESSION['error'] = "Course ID, name and credits are required.";
    }

    if ($DEBUG_MODE) {
        debug_dump('Redirect Skipped', 'DEBUG_MODE=1 prevents redirect so you can read debug output.');
    } else {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

/* ---------- DELETE COURSE ---------- */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    debug_dump('RAW $_GET', $_GET);
    debug_dump('Delete Params', ['course_id' => $id, 'types' => 's']);
    $sql = "DELETE FROM courses WHERE course_id = ?";
    debug_dump('Delete SQL', $sql);

    try {
        $d = $conn->prepare($sql);
        $d->bind_param("s", $id);
        $ok = $d->execute();

        debug_dump('Execute Result (Delete)', [
            'ok' => $ok,
            'affected_rows' => $d->affected_rows
        ]);

        $_SESSION['message'] = $d->affected_rows ? "Course “{$id}” deleted." : "Course not found.";
        $d->close();
    } catch (mysqli_sql_exception $e) {
        debug_dump('MySQL Exception (Delete)', $e->getMessage());
        $_SESSION['error'] = "Cannot delete course; it is referenced elsewhere.";
    }

    if ($DEBUG_MODE) {
        debug_dump('Redirect Skipped', 'DEBUG_MODE=1 prevents redirect so you can read debug output.');
    } else {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

/* ---------- FETCH LIST ---------- */
$sql_list = "SELECT course_id, course_name, description, credits FROM courses ORDER BY course_id ASC";
debug_dump('List SQL', $sql_list);

try {
    $list = $conn->prepare($sql_list);
    $list->execute();
    $courses = $list->get_result();
} catch (mysqli_sql_exception $e) {
    debug_dump('MySQL Exception (List)', $e->getMessage());
    $_SESSION['error'] = "Could not fetch course list.";
    // Fail softly with empty result set
    $courses = new ArrayObject([]);
}

/* ---------- NOW OUTPUT HTML ---------- */
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
?>
<div class="card">
    <h2>Manage Courses</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <p style="color:green;margin:8px 0;"><?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['message']); ?></p>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red;margin:8px 0;"><?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <h3>Add New Course</h3>
    <form method="POST" action="">
        <div class="form-group">
            <label for="course_id">Course ID</label>
            <input id="course_id" name="course_id" type="text" required>
        </div>
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input id="course_name" name="course_name" type="text" required>
        </div>
        <div class="form-group">
            <label for="description">Description (optional)</label>
            <textarea id="description" name="description" rows="2"></textarea>
        </div>
        <div class="form-group">
            <label for="credits">Credits</label>
            <input id="credits" name="credits" type="number" min="1" max="10" required>
        </div>
        <button type="submit" name="add_course" class="btn">Add Course</button>
    </form>

    <hr style="margin:18px 0; border:0; border-top:1px solid #eee;">

    <table class="table">
        <tr>
            <th>Course ID</th>
            <th>Course Name</th>
            <th>Credits</th>
            <th>Action</th>
        </tr>
        <?php if ($courses instanceof mysqli_result): ?>
            <?php while ($c = $courses->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['course_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td title="<?php echo htmlspecialchars($c['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($c['course_name'], ENT_QUOTES, 'UTF-8'); ?>
                    </td>
                    <td><?php echo (int)$c['credits']; ?></td>
                    <td>
                        <a href="edit_course.php?id=<?php echo urlencode($c['course_id']); ?>">Edit</a> |
                        <a href="course.php?delete=<?php echo urlencode($c['course_id']); ?><?php echo $DEBUG_MODE ? '&debug=1' : ''; ?>"
                           onclick="return confirm('Delete course <?php echo htmlspecialchars($c['course_id'], ENT_QUOTES, 'UTF-8'); ?>?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
    </table>

    <?php if ($DEBUG_MODE): ?>
        <p style="margin-top:12px;color:#555;font-size:12px;">
            Debug mode is ON. Remove <code>?debug=1</code> from the URL to return to normal behavior.
        </p>
    <?php endif; ?>
</div>
</main></div></body></html>