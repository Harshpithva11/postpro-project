<?php
session_start();
if(!isset($_SESSION['is_admin']))
{
    header("Location:admin_login.php");
    exit();
}
if(isset($_GET['id']))
{
    $id=intval($_GET['id']);
    require_once __DIR__ . '/admin_db.php';
    $conn->query("DELETE FROM songs WHERE id = $id");
    $conn->close();
}
header("Location:admin_add_song.php");
exit();
?>