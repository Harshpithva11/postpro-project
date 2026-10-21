<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['delete_id'])) {
    header("Location: admin_manage_hashtag.php");
    exit();
}

$delete_id = intval($_GET['delete_id']);

require_once __DIR__ . '/admin_db.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("DELETE FROM hashtags WHERE hashtag_id = ?");
$stmt->bind_param("i", $delete_id);
$stmt->execute();
$stmt->close();

$conn->close();

header("Location: admin_manage_hashtag.php");
exit();
