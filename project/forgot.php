<?php
session_start();
include 'db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = trim($_POST["username_or_email"]);

    if (empty($input)) {
        $message = "❌ Please enter your username or email.";
    } else {
        $sql = "SELECT * FROM user_tbl WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $input, $input);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['reset_user_id'] = $user['user_id'];
            header("Location: reset_password.php");
            exit();
        } else {
            $message = "❌ No user found with that username or email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="form-container">
    <h1>Forgot Password</h1>
    <form method="POST">
        <div class="input-box">
            <input type="text" name="username_or_email" placeholder="Username or Email" required>
            <i class="fa-solid fa-user"></i>
        </div>
        <button type="submit">Find Account</button>
    </form>
    
    <?php if (!empty($message)): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <a href="login.php"><i class="fa-solid fa-arrow-left"></i> Back to Login</a>
</div>

</body>
</html>
