<?php
include("db/config.php");
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO Faculty (faculty_id, first_name, last_name, email, password, department_id, designation, joining_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $_POST['faculty_id'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'], $_POST['department_id'], $_POST['designation'], $_POST['joining_date']);
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
  <title>Faculty Registration | SCS</title>
  <link rel="stylesheet" href="css/login.css">
</head>
<body>
  <div class="login-box">
    <div class="login-header">
      <h1>SCS</h1>
    </div>
    <form method="POST">
      <label>Faculty ID</label>
      <input type="text" name="faculty_id" required>

      <label>First Name</label>
      <input type="text" name="first_name" required>

      <label>Last Name</label>
      <input type="text" name="last_name" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Password</label>
      <input type="password" name="password" required>

      <label>Department ID</label>
      <input type="text" name="department_id" required>

      <label>Designation</label>
      <input type="text" name="designation" required>

      <label>Joining Date</label>
      <input type="date" name="joining_date" required>

      <button type="submit">Register</button>
    </form>
    <div class="footer-links"><?php echo $success; ?></div>
  </div>
</body>
</html>