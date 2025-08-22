<?php
// --- INCLUDES and DATABASE CONNECTION ---
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

// --- DATABASE CONNECTION CHECK ---
if (!$conn || $conn->connect_error) {
    die("FATAL ERROR: Database connection failed: " . $conn->connect_error);
}

// --- FORM SUBMISSION LOGIC (ADD & EDIT) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_grade'])) {
    $student_id = trim($_POST['student_id']);
    $course_id = trim($_POST['course_id']);
    $marks = $_POST['marks'];
    $grade = trim($_POST['grade']);

    // Simplified SQL for maximum reliability
    $sql = "INSERT INTO results (student_id, course_id, marks_obtained, grade)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained), grade = VALUES(grade)";
            
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("ERROR: SQL query preparation failed: " . $conn->error);
    }
    
    $stmt->bind_param("ssds", $student_id, $course_id, $marks, $grade);

    if (!$stmt->execute()) {
        die("ERROR: Could not save the grade. " . $stmt->error);
    }
    
    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- EDIT MODE LOGIC ---
$is_edit_mode = false;
$grade_to_edit = ['student_id' => '', 'course_id' => '', 'marks_obtained' => '', 'grade' => ''];
if (isset($_GET['edit']) && isset($_GET['student_id']) && isset($_GET['course_id'])) {
    $is_edit_mode = true;
    $edit_stmt = $conn->prepare("SELECT student_id, course_id, marks_obtained, grade FROM results WHERE student_id = ? AND course_id = ?");
    $edit_stmt->bind_param("ss", $_GET['student_id'], $_GET['course_id']);
    $edit_stmt->execute();
    $result = $edit_stmt->get_result();
    if ($result->num_rows > 0) {
        $grade_to_edit = $result->fetch_assoc();
    }
    $edit_stmt->close();
}

// --- DELETE LOGIC ---
if (isset($_GET['delete']) && isset($_GET['student_id']) && isset($_GET['course_id'])) {
    $delete_stmt = $conn->prepare("DELETE FROM results WHERE student_id = ? AND course_id = ?");
    $delete_stmt->bind_param("ss", $_GET['student_id'], $_GET['course_id']);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- DATA FETCHING FOR PAGE DISPLAY ---
$all_students = $conn->query("SELECT student_id, first_name, last_name FROM students ORDER BY first_name, last_name")->fetch_all(MYSQLI_ASSOC);
$all_courses = $conn->query("SELECT course_id, course_name FROM courses ORDER BY course_name")->fetch_all(MYSQLI_ASSOC);
$grades_result = $conn->query("SELECT r.student_id, r.course_id, s.first_name, s.last_name, c.course_name, r.marks_obtained, r.grade
                               FROM results r
                               JOIN students s ON r.student_id = s.student_id
                               JOIN courses c ON r.course_id = c.course_id
                               ORDER BY s.last_name, s.first_name, c.course_name");
?>

<div class="card">
    <h2><?php echo $is_edit_mode ? 'Edit Grade' : 'Manage Grades'; ?></h2>
    <h3><?php echo $is_edit_mode ? 'Update Grade Details' : 'Add New Grade'; ?></h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <label for="student_id">Student:</label>
        <select name="student_id" required <?php if ($is_edit_mode) echo 'disabled'; ?>>
            <option value="">Select Student</option>
            <?php foreach ($all_students as $student): ?>
                <option value="<?php echo $student['student_id']; ?>" <?php if ($grade_to_edit['student_id'] == $student['student_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($is_edit_mode): ?>
            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($grade_to_edit['student_id']); ?>">
        <?php endif; ?>
        <label for="course_id">Course:</label>
        <select name="course_id" required <?php if ($is_edit_mode) echo 'disabled'; ?>>
            <option value="">Select Course</option>
            <?php foreach ($all_courses as $course): ?>
                <option value="<?php echo $course['course_id']; ?>" <?php if ($grade_to_edit['course_id'] == $course['course_id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($course['course_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($is_edit_mode): ?>
            <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($grade_to_edit['course_id']); ?>">
        <?php endif; ?>
        <label for="marks">Marks:</label>
        <input type="number" step="any" name="marks" placeholder="Enter marks" value="<?php echo htmlspecialchars($grade_to_edit['marks_obtained']); ?>" required>
        <label for="grade">Grade:</label>
        <input type="text" name="grade" placeholder="Enter grade (e.g., A, B, etc.)" value="<?php echo htmlspecialchars($grade_to_edit['grade']); ?>" required>
        <button type="submit" name="submit_grade"><?php echo $is_edit_mode ? 'Update Grade' : 'Save Grade'; ?></button>
        <?php if ($is_edit_mode): ?>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="button-cancel">Cancel</a>
        <?php endif; ?>
    </form>
</div>
<div class="card">
    <h3>All Grades</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Course</th>
                <th>Marks</th>
                <th>Grade</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($grades_result && $grades_result->num_rows > 0): ?>
                <?php while ($grade_row = $grades_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grade_row['first_name'] . ' ' . $grade_row['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($grade_row['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($grade_row['marks_obtained']); ?></td>
                        <td><?php echo htmlspecialchars($grade_row['grade']); ?></td>
                        <td>
                            <a href="?edit=true&student_id=<?php echo $grade_row['student_id']; ?>&course_id=<?php echo $grade_row['course_id']; ?>">Edit</a> |
                            <a href="?delete=true&student_id=<?php echo $grade_row['student_id']; ?>&course_id=<?php echo $grade_row['course_id']; ?>" onclick="return confirm('Are you sure you want to delete this grade?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No grades found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>