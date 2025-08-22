<?php
require_once __DIR__ . '/../includes/header_admin.php';
require_once __DIR__ . '/../db/config.php';

$course_id = $_GET['id'] ?? '';
if ($course_id === '') {
    $_SESSION['error'] = "Missing course id.";
    header("Location: course.php"); exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM Courses WHERE course_id = ?");
    $stmt->bind_param("s", $course_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Course “{$course_id}” deleted.";
    } else {
        $_SESSION['error'] = "Course not found or could not be deleted.";
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    // FK constraint likely blocking
    $_SESSION['error'] = "Cannot delete this course because it is referenced elsewhere.";
}

header("Location: course.php");
exit;