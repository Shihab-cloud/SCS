<?php
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

if (isset($_GET['id'])) {
    $faculty_id = $_GET['id'];

    // Delete the faculty member
    $stmt = $conn->prepare("DELETE FROM Faculty WHERE faculty_id = ?");
    $stmt->bind_param("s", $faculty_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Faculty deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting faculty.";
    }

    // Redirect to faculty list
    header("Location: faculty.php");
    exit();
}
?>

</main></div></body></html>