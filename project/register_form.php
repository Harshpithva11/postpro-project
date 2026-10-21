<?php
session_start();

$formError = isset($_SESSION['form_error']);
$hasMessage = isset($_SESSION['message']);
$isSuccess = $hasMessage && strpos($_SESSION['message'], '✅') !== false;

if (!$formError && !$isSuccess) {
    unset($_SESSION['form_email']);
    unset($_SESSION['form_username']);
    unset($_SESSION['message']);
}

unset($_SESSION['form_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Page</title>
    <link rel="stylesheet" href="register_form.css"> <!-- External CSS -->

    <!-- Font Awesome for eye icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Inline styling for form fields and toggle buttons -->
    <style>
        .input-box {
            position: relative;
        }

        .input-box input {
            width: 100%;
            padding-right: 40px; /* Make space for eye icon */
        }

        .toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 18px;
            z-index: 2;
            color: #fff;
        }

        .toggle-btn:focus {
            outline: none;
        }

        .error-message {
            color: #ff004d;
            background: rgba(0, 136, 255, 0.15);
            padding: 8px 12px;
            margin-bottom: 10px;
            border-radius: 5px;
            font-weight: 500;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-box">
            <h1>Register Page</h1>
            <form action="register_process.php" method="POST"> 

                <?php
                // Display any feedback/error message
                if (isset($_SESSION['message'])) {
                    echo '<div class="error-message">' . $_SESSION['message'] . '</div>';
                    unset($_SESSION['message']); // Clear message after showing
                }

                // Pre-fill form values if available (e.g. after validation error)
                $emailValue = isset($_SESSION['form_email']) ? htmlspecialchars($_SESSION['form_email']) : '';
                $usernameValue = isset($_SESSION['form_username']) ? htmlspecialchars($_SESSION['form_username']) : '';
                ?>

                <!-- Email input -->
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required value="<?= $emailValue ?>">
                </div>

                <!-- Username input -->
                <div class="input-box">
                    <input type="text" name="username" placeholder="Username" required value="<?= $usernameValue ?>">
                </div>

                <div class="input-box">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <button type="button" class="toggle-btn" onclick="togglePassword('password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <!-- Confirm Password input with toggle eye icon -->
                <div class="input-box">
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                    <button type="button" class="toggle-btn" onclick="togglePassword('confirm_password', this)">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>

                <!-- Remember me checkbox and forgot link -->
                <div class="remember-forgot">
                    <label><input type="checkbox" name="remember"> Remember me</label>
                    <a href="forgot.php">Forgot password?</a>
                </div>

                <!-- Submit button -->
                <button type="submit" class="submit-btn">Submit</button>

                <!-- Link to login page -->
                <div class="extra-links">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript function to toggle password visibility -->
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId); // Get password input
            const icon = button.querySelector('i'); // Get the icon inside the button
            const isPassword = input.type === 'password'; // Check input type

            input.type = isPassword ? 'text' : 'password'; // Toggle type
            icon.classList.toggle('fa-eye', !isPassword); // Toggle icon
            icon.classList.toggle('fa-eye-slash', isPassword);
        }
    </script>
</body>
</html>
