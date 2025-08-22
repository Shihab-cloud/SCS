<?php
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

if (isset($_GET['id'])) {
    $student_id = $_GET['id'];

    // First, delete any records in related tables that reference this student
    $stmt = $conn->prepare("DELETE FROM enrollments WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM attendance WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->close();

    // Then, delete the student
    $stmt = $conn->prepare("DELETE FROM Students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Student deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting student.";
    }

    // Redirect to student list
    header("Location: student.php");
    exit();
}
?>

</main></div></body></html>