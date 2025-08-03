<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome | Smart Cloud System</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=ADLaM+Display&display=swap" rel="stylesheet">
</head>
<body>

  <!-- Landing Animation -->
  <div class="container bg1">
    <div class="overlay">
      <h1 class="welcome-text">Welcome to Smart Cloud Student Enrollment & Management System</h1>
    </div>
  </div>

  <!-- Intermediate Layout: Title + Features -->
  <div class="final-layout-slanted hidden" id="finalLayout">
    <div class="left-panel"></div>
    <div class="right-panel">
      <div class="title" id="final-title">SMART CLOUD STUDENT ENROLLMENT AND<br>MANAGEMENT SYSTEM</div>
      <div class="features-box" id="features-box">
        <h2 class="features-heading">Features:</h2>
        <ol class="features-list">
          <li>User Authentication</li>
          <li>Student Admission Management</li>
          <li>Course Registration</li>
          <li>Academic Records Management</li>
          <li>Progress Tracking</li>
          <li>Reporting Tracking</li>
          <li>Reporting Features</li>
          <li>Admin Panel</li>
        </ol>
      </div>
    </div>
  </div>

  <!-- Final Clean Login Page -->
  <div class="final-layout-clean hidden" id="cleanLayout">
    <div class="left-panel-final"></div>
    <div class="right-panel-final">
      <div class="login-btn-final hidden" id="login-button">
        <a href="login_step1.php">Login</a>
      </div>
    </div>
  </div>

  <script>
    const welcome = document.querySelector('.welcome-text');
    const container = document.querySelector('.container');
    const finalLayout = document.getElementById('finalLayout');
    const cleanLayout = document.getElementById('cleanLayout');
    const finalTitle = document.getElementById('final-title');
    const featuresBox = document.getElementById('features-box');
    const loginButton = document.getElementById('login-button');

    welcome.addEventListener('animationend', () => {
      welcome.style.display = 'none';
      container.classList.add('slide-left');

      setTimeout(() => {
        container.style.display = 'none';
        finalLayout.classList.remove('hidden');
        finalLayout.classList.add('fly-in');

        setTimeout(() => {
          finalTitle.classList.add('fade-out');
          featuresBox.classList.add('fade-out');

          setTimeout(() => {
            finalLayout.classList.add('hidden');
            cleanLayout.classList.remove('hidden');
            loginButton.classList.remove('hidden');
            loginButton.classList.add('fade-in');
          }, 1500);
        }, 4000);
      }, 1000);
    });
  </script>

</body>
</html>
