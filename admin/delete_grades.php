<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db/config.php';

$student_id = $_GET['student_id'] ?? '';
$course_id  = $_GET['course_id']  ?? '';

if ($student_id === '' || $course_id === '') {
    $_SESSION['error'] = "Missing identifiers.";
    header("Location: grades.php"); exit;
}

try {
    $d = $conn->prepare("DELETE FROM results WHERE student_id=? AND course_id=?");
    $d->bind_param("ss", $student_id, $course_id);
    $d->execute();
    $_SESSION['message'] = $d->affected_rows ? "Grade deleted." : "Grade not found.";
    $d->close();
} catch (mysqli_sql_exception $e) {
    $_SESSION['error'] = "Could not delete grade.";
}

header("Location: grades.php");
exit;