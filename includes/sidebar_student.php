<div class="sidebar">
  <div class="brand">SCS Student</div>
  <button class="sidebar-toggle" data-toggle="sidebar">☰</button>
  <ul>
    <li><a href="/smart_cloud_system/student/dashboard.php">Dashboard</a></li>
    <li><a href="/smart_cloud_system/student/courses.php">My Courses</a></li>
    <li><a href="/smart_cloud_system/student/attendance.php">Attendance</a></li>
    <li><a href="/smart_cloud_system/student/grades.php">Grades</a></li>
    <li><a href="/smart_cloud_system/student/notices.php">Notices</a></li>
    <li><a href="/smart_cloud_system/student/profile.php">Profile</a></li>
    <li><a href="/smart_cloud_system/student/settings.php">Settings</a></li>
  </ul>
  <div class="sidebar-footer">
    <div class="me">👤 <?php echo htmlspecialchars($STUDENT_ID); ?></div>
    <a class="logout" href="/smart_cloud_system/login_step1.php">Logout</a>
  </div>
</div>
<main class="content">