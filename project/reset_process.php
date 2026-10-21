<?php
session_start();
include 'db.php';

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot.php");
    exit();
}

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = trim($_POST["new_password"]);

    if (empty($new_password)) {
        $message = "❌ Please enter a new password.";
    } elseif (strlen($new_password) < 6) {
        $message = "❌ Password must be at least 6 characters long.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE user_tbl SET password = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $hashed, $_SESSION['reset_user_id']);
        if (mysqli_stmt_execute($stmt)) {
            $message = "✅ Password has been reset successfully.";
            session_destroy();
        } else {
            $message = "❌ Something went wrong.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="reset_password.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>

        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">
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
