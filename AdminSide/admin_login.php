<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';


    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['is_admin'] = true;   //enable for forward to dashboard page
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "❌ Invalid admin credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - POSTPRO</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1f1c2c, #928dab);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .login-container {
            background-color: rgba(0, 0, 0, 0.75);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 25px;
            font-size: 28px;
            letter-spacing: 1px;
            color: #f1f1f1;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            background-color: #eee;
            font-size: 16px;
            color: #333;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: #007BFF;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: #ff4d4d;
            margin-top: 15px;
            font-weight: bold;
        }

        .admin-label {
            font-size: 13px;
            color: #aaa;
            margin-top: 30px;
        }
        @media (max-width: 600px) {
            body {
                height: auto !important;
                min-height: 100vh !important;
                padding: 20px 10px !important;
            }
            .login-container {
                padding: 30px 18px !important;
                width: 95% !important;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>🔐 Admin Login POSTPRO</h2>
        
    <form method="POST"  autocomplete="off">
    <input type="text" name="username" placeholder="Admin Username" autocomplete="off" />
    <input type="password" name="password" placeholder="Password" autocomplete="new-password" />
    <button type="submit">Login</button>
</form>


        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <div class="admin-label">Developer Access Only</div>
    </div>
</body>
</html>
