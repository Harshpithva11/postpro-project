<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        $_SESSION['message'] = "❌ Please enter both username and password.";
        header("Location: login.php");
        exit;
    }

    // Fetch user_id and hashed password for the username
    $sql = "SELECT user_id, password FROM user_tbl WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result($stmt, $user_id, $hashed_password);
            mysqli_stmt_fetch($stmt);

            if (password_verify($password, $hashed_password)) {
                // Success - store user_id AND username in session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                header("Location: postpro.php");
                exit;
            } else {
                $_SESSION['message'] = "❌ Invalid password.";
            }
        } else {
            $_SESSION['message'] = "❌ Username not found.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['message'] = "❌ Login failed: " . mysqli_error($conn);
    }

    mysqli_close($conn);
    header("Location: login.php");
    exit;
}
?>
