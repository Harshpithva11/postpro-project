<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="reset_password.css">

    <!-- Font Awesome for eye icon -->
   <!-- // <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <form action="reset_process.php" method="POST">
            <?php
            session_start();
            if (isset($_SESSION['message'])) {
                echo '<div class="message success">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']);
            }
            ?>

            <div class="input-box">
                <input type="password" name="new_password" id="new_password" placeholder="Enter New Password" required>
                <button type="button" class="toggle-btn" onclick="togglePassword('new_password', this)">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>

            <button type="submit" class="submit-btn">Reset Password</button>

            <div class="extra-links">
                <a href="login.php">← Back to Login</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);
        }
    </script>
</body>
</html>
