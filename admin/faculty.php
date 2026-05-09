<?php
require_once __DIR__ . '/../includes/header_admin.php';
include __DIR__ . '/../includes/sidebar_admin.php';
require_once __DIR__ . '/../db/config.php';

// Fetching faculty data
$stmt = $conn->prepare("SELECT Distinct * FROM Faculty");
$stmt->execute();
$faculty_result = $stmt->get_result();
?>

<div class="card">
    <h2>Manage Faculty</h2>
    <table class="table">
        <tr>
            <th>Faculty ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php while ($faculty = $faculty_result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($faculty['faculty_id']); ?></td>
            <td><?php echo htmlspecialchars($faculty['first_name']); ?></td>
            <td><?php echo htmlspecialchars($faculty['last_name']); ?></td>
            <td><?php echo htmlspecialchars($faculty['email']); ?></td>
            <td>
                <a href="edit_faculty.php?id=<?php echo $faculty['faculty_id']; ?>">Edit</a> | 
                <a href="delete_faculty.php?id=<?php echo $faculty['faculty_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</main></div></body></html>