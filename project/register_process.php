<?php
session_start(); 
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION['form_email'] = trim($_POST["email"]);
    $_SESSION['form_username'] = trim($_POST["username"]);
    $_SESSION['form_error'] = true; 

    $email = $_SESSION['form_email'];
    $username = $_SESSION['form_username'];
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Check if any field is empty
    if (empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['message'] = "❌ Please fill in all fields."; 
        header("Location: register_form.php"); // Redirect back to registration form
        exit(); 
    }

    // Check if password length is at least 6 characters
    if (strlen($password) < 6) {
        $_SESSION['message'] = "❌ Password must be at least 6 characters long.";
        header("Location: register_form.php");
        exit();
    }

    // Check if password and confirm_password match
    if ($password !== $confirm_password) {
        $_SESSION['message'] = "❌ Passwords do not match.";
        header("Location: register_form.php");
        exit();
    }

    // Prepare SQL to check if the email or username already exists in the database
    $check_sql = "SELECT user_id FROM user_tbl WHERE email = ? OR username = ?";
    $stmt = mysqli_prepare($conn, $check_sql); 

    if (!$stmt) {
        // If preparation fails, return error
        $_SESSION['message'] = "❌ Database error: Failed to prepare SELECT.";
        header("Location: register_form.php");
        exit();
    }

    // Bind email and username to the SQL query (protection against SQL injection)
    mysqli_stmt_bind_param($stmt, "ss", $email, $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt); 

    // If any row is found, that means email or username already exists
    if (mysqli_stmt_num_rows($stmt) > 0) {
        $_SESSION['message'] = "❌ Email or Username already exists.";
        mysqli_stmt_close($stmt); 
        header("Location: register_form.php"); 
        exit();
    }
    mysqli_stmt_close($stmt); 

    // If validation passed, hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL to insert the new user
    $insert_sql = "INSERT INTO user_tbl (email, username, password) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql); 

    if (!$insert_stmt) {
        $_SESSION['message'] = "❌ Database error: Failed to prepare INSERT.";
        header("Location: register_form.php");
        exit();
    }

    // Bind user data to the insert query
    mysqli_stmt_bind_param($insert_stmt, "sss", $email, $username, $hashed_password);

    // Execute the insert query
    if (mysqli_stmt_execute($insert_stmt)) {
        $_SESSION['message'] = "✅ Registration successful!"; 
        // Clear form-related session variables
        unset($_SESSION['form_email']);
        unset($_SESSION['form_username']);
        unset($_SESSION['form_error']);
    } else {
        $_SESSION['message'] = "❌ Insertion failed. Please try again."; 
    }

    // Clean up and close connections
    mysqli_stmt_close($insert_stmt);
    mysqli_close($conn);

    // Redirect back to the form (with success or failure message)
    header("Location: register_form.php");
    exit();
}
?>
