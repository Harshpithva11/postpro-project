<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/admin_db.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM song_tbl WHERE song_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_manage_song.php");
    exit();
}

// Fetch songs ordered by song_id ascending
$sql = "SELECT song_id, song_title, mood, song_link FROM song_tbl ORDER BY song_id ASC";
$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Songs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fb;
            margin: 20px;
        }
        h1 {
            color: #6a5af9;
            text-align: center;
        }
        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            word-break: break-word;
        }
        th {
            background-color: #6a5af9;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a.btn {
            padding: 7px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            color: white;
            margin-right: 8px;
            display: inline-block;
        }
        a.edit {
            background-color: #00b894;
        }
        a.edit:hover {
            background-color: #009f7b;
        }
        a.delete {
            background-color: #d63031;
        }
        a.delete:hover {
            background-color: #b02626;
        }
        .top-link {
            display: block;
            width: 95%;
            margin: 20px auto;
            text-align: right;
        }
        .top-link a {
            text-decoration: none;
            font-weight: 600;
            color: #6a5af9;
        }
        /* For the link field, style anchor */
        a.song-link {
            color: #0984e3;
            text-decoration: none;
            font-weight: 600;
        }
        a.song-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Manage Songs</h1>
<div class="top-link"><a href="admin_dashboard.php">← Back to Dashboard</a></div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Song Title</th>
            <th>Mood</th>
            <th>Song Link</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['song_id']) ?></td>
            <td><?= htmlspecialchars($row['song_title']) ?></td>
            <td><?= htmlspecialchars(ucfirst($row['mood'])) ?></td>
            <td>
                <a href="<?= htmlspecialchars($row['song_link']) ?>" target="_blank" class="song-link">Listen</a>
            </td>
            <td>
                <a href="admin_edit_song.php?id=<?= $row['song_id'] ?>" class="btn edit">Edit</a>
                <a href="admin_manage_song.php?delete_id=<?= $row['song_id'] ?>" class="btn delete"
                   onclick="return confirm('Are you sure you want to delete this song?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5" style="text-align:center;">No songs found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
