<nav class="sidebar">
  <div class="brand">SCS Faculty</div>
  <button class="sidebar-toggle" data-toggle="sidebar">☰</button>
  <ul>
    <li><a href="/smart_cloud_system/faculty/dashboard.php">Dashboard</a></li>
    <li><a href="/smart_cloud_system/faculty/courses.php">My Courses</a></li>
    <li><a href="/smart_cloud_system/faculty/schedule.php">Schedule</a></li>
    <li><a href="/smart_cloud_system/faculty/notices.php">Notices</a></li>
    <li><a href="/smart_cloud_system/faculty/settings.php">Settings</a></li>
  </ul>
  <div class="sidebar-footer">
    <div class="me">👤 <?php echo htmlspecialchars($FACULTY_ID); ?></div>
    <a class="logout" href="/smart_cloud_system/login_step1.php">Logout</a>
  </div>
</nav>
<main class="content">