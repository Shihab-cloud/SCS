<div class="sidebar">
  <div class="brand">SCS Student</div>
  <button class="sidebar-toggle" data-toggle="sidebar">☰</button>
  <ul>
    <li><a href="../student/dashboard.php">Dashboard</a></li>
    <li><a href="../student/courses.php">My Courses</a></li>
    <li><a href="../student/register_course.php">Register Courses</a></li>
    <li><a href="../student/attendance.php">Attendance</a></li>
    <li><a href="../student/grades.php">Grades</a></li>
    <li><a href="../student/notices.php">Notices</a></li>
    <li><a href="../student/profile.php">Profile</a></li>
    <li><a href="../student/settings.php">Settings</a></li>
  </ul>
  <div class="sidebar-footer">
        <div class="me">👤 <?php echo htmlspecialchars($_SESSION['login_user']); ?></div>
        <a class="logout" href="../login_step1.php">Logout</a>
  </div>
</div>
<main class="content">