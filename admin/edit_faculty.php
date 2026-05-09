<?php
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

if (isset($_GET['id'])) {
    $faculty_id = $_GET['id'];

    // Fetch the faculty details
    $stmt = $conn->prepare("SELECT * FROM Faculty WHERE faculty_id = ?");
    $stmt->bind_param("s", $faculty_id);
    $stmt->execute();
    $faculty = $stmt->get_result()->fetch_assoc();
}

// Update faculty details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE Faculty SET first_name = ?, last_name = ?, email = ? WHERE faculty_id = ?");
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $faculty_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Faculty updated successfully!";
        header("Location: faculty.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating faculty.";
    }
}
?>

<div class="card">
    <h2>Edit Faculty</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($faculty['first_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($faculty['last_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($faculty['email']); ?>" required>
        </div>
        <button type="submit" class="btn">Update Faculty</button>
    </form>
</div>

</main></div></body></html>