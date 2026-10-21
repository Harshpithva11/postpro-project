<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login Form</title>
  <link rel="stylesheet" href="login.css" />

  <!-- Font Awesome for icons -->
  <link
    rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>

  <div class="top-links">
    <a href="postpro.php">HomePage</a>
    <a href="aboutus.php">About Us</a>
    <a href="feedback.php">FeedBack</a>
  </div>


  <div class="background">
    <div class="login-container">
      <h1>Login Page</h1>
      <form action="login_process.php" method="POST">
        <!-- Error message -->
        <?php
        if (isset($_SESSION['message'])) {
          echo '<div class="error-message">' . $_SESSION['message'] . '</div>';
          unset($_SESSION['message']);
        }
        ?>
        <!-- Username Field -->
        <div class="input-group">
          <input
            type="text"
            id="username"
            class="input-field"
            placeholder="Username"
            name="username"
            required
          />
          <i class="fa-solid fa-user icon"></i>
        </div>

        <!-- Password Field -->
        <div class="input-group">
          <input
            type="password"
            id="password"
            class="input-field"
            placeholder="Password"
            name="password"
            required
          />
          <button type="button" class="toggle-btn" onclick="togglePassword()">
            <i class="fa-solid fa-eye" id="toggle-icon"></i>
          </button>
        </div>

        <div class="options">
          <label><input type="checkbox" /> Remember me</label>
          <a href="forgot.php">Forgot password?</a>
        </div>
        <button type="submit" class="login-btn">Login</button>
        
        <p class="register-text">
          Don't have an account? <a href="register_form.php">Register</a>
        </p>
      </form>
    </div>
  </div>

  <!-- Toggle Password Script -->
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      const icon = document.getElementById("toggle-icon");
      const isPassword = passwordInput.type === "password";

      passwordInput.type = isPassword ? "text" : "password";
      icon.classList.toggle("fa-eye", !isPassword);
      icon.classList.toggle("fa-eye-slash", isPassword);
    }
  </script>
</body>
</html>
