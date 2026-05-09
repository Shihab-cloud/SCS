<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | SCS</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <div class="login-box">
    <div class="login-header">
      <h1>SCS</h1>
    </div>
    <form method="GET" action="">
      <label>You are</label>
      <select name="role" required style="width:100%; padding:10px; font-family:'Times New Roman', serif; margin-bottom: 20px;">
        <option value="">Select...</option>
        <option value="student">Student</option>
        <option value="faculty">Faculty</option>
      </select>
      <button type="submit">Next</button>
    </form>
  </div>

  <?php
    if (isset($_GET['role'])) {
        $role = $_GET['role'];
        if ($role === 'student') {
            header("Location: student_register.php");
            exit();
        } elseif ($role === 'faculty') {
            header("Location: faculty_register.php");
            exit();
        }
    }
  ?>
</body>
</html>