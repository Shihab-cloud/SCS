<div class="sidebar">
  <div class="brand">SCS Admin</div>
  <button class="sidebar-toggle" data-toggle="sidebar">☰</button>
  <ul>
    <li><a href="../admin/dashboard.php">Dashboard</a></li>
    <li><a href="../admin/faculty.php">Manage Faculty</a></li>
    <li><a href="../admin/student.php">Manage Students</a></li>
    <li><a href="../admin/course.php">Manage Courses</a></li>
    <li><a href="../admin/grades.php">Manage Grades</a></li>
    <li><a href="../admin/settings.php">Admin Profile & Settings</a></li>
  </ul>
  <div class="sidebar-footer">
    <div class="me">👤 <?php echo htmlspecialchars($ADMIN_ID); ?></div>
    <a class="logout" href="../login_step1.php">Logout</a>
  </div>
</div>
<main class="content">