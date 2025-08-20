<div class="sidebar">
  <div class="brand">SCS Admin</div>
  <button class="sidebar-toggle" data-toggle="sidebar">☰</button>
  <ul>
    <li><a href="/smart_cloud_system/admin/dashboard.php">Dashboard</a></li>
    <li><a href="/smart_cloud_system/admin/manage_faculty.php">Manage Faculty</a></li>
    <li><a href="/smart_cloud_system/admin/manage_students.php">Manage Students</a></li>
    <li><a href="/smart_cloud_system/admin/manage_courses.php">Manage Courses</a></li>
    <li><a href="/smart_cloud_system/admin/settings.php">Settings</a></li>
  </ul>
  <div class="sidebar-footer">
    <div class="me">👤 <?php echo htmlspecialchars($ADMIN_ID); ?></div>
    <a class="logout" href="/smart_cloud_system/login_step1.php">Logout</a>
  </div>
</div>
<main class="content">