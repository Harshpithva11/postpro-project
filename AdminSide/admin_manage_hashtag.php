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
    $stmt = $conn->prepare("DELETE FROM hashtags WHERE hashtag_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_manage_hashtag.php");
    exit();
}

// Fetch hashtags (excluding null or empty hashtag_text) in ascending order
$sql = "SELECT hashtag_id, mood, hashtag_text
        FROM hashtags
        WHERE hashtag_text IS NOT NULL AND TRIM(hashtag_text) != ''
        ORDER BY hashtag_id ASC";
$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Hashtags</title>
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
            width: 90%;
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
            width: 90%;
            margin: 20px auto;
            text-align: right;
        }
        .top-link a {
            text-decoration: none;
            font-weight: 600;
            color: #6a5af9;
        }
    </style>
</head>
<body>

<h1>Manage Hashtags</h1>
<div class="top-link"><a href="admin_dashboard.php">← Back to Dashboard</a></div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Mood</th>
            <th>Hashtag Text</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()):
            $hashtag = trim($row['hashtag_text']);
            if ($hashtag === '') continue; // skip if still blank
        ?>
        <tr>
            <td><?= htmlspecialchars($row['hashtag_id']) ?></td>
            <td><?= htmlspecialchars(ucfirst($row['mood'])) ?></td>
            <td><?= htmlspecialchars($hashtag) ?></td>
            <td>
                <a href="admin_edit_hashtag.php?id=<?= $row['hashtag_id'] ?>" class="btn edit">Edit</a>
                <a href="admin_manage_hashtag.php?delete_id=<?= $row['hashtag_id'] ?>" class="btn delete"
                   onclick="return confirm('Are you sure you want to delete this hashtag?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">No hashtags found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>
