<?php
include("db/config.php");
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO Students (student_id, first_name, last_name, email, password, date_of_birth) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $_POST['student_id'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['dob']);
    if ($stmt->execute()) {
        $success = "Registration successful!";
    } else {
        $success = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Student Registration | SCS</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <div class="login-box">
    <div class="login-header">
      <h1>SCS</h1>
    </div>
    <form method="POST">
      <label>Student ID</label>
      <input type="text" name="student_id" required>

      <label>First Name</label>
      <input type="text" name="first_name" required>

      <label>Last Name</label>
      <input type="text" name="last_name" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Date of Birth</label>
      <input type="date" name="dob" required>

      <button type="submit">Register</button>
    </form>
    <div class="footer-links"><?php echo $success; ?></div>
  </div>
</body>
</html>