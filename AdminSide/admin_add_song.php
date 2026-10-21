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

// Handle song submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $song_title = trim($_POST["song_title"]);
    $mood = trim($_POST["mood"]);
    $song_link = trim($_POST["song_link"]);

    if (!empty($song_title) && !empty($mood) && !empty($song_link)) {
        $stmt = $conn->prepare("INSERT INTO song_tbl (song_title, mood, song_link) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $song_title, $mood, $song_link);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('✅ Song added successfully!');</script>";
    } else {
        echo "<script>alert('⚠️ All fields are required.');</script>";
    }
}

// Fetch unique moods from song_tbl table
$moods = [];
$result = $conn->query("SELECT DISTINCT mood FROM song_tbl ORDER BY mood ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $moods[] = $row['mood'];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Song</title>
    <style>
        body {
            background: #1e2a38;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            margin: 0;
        }

        .form-box {
            background: rgba(255, 255, 255, 0.08);
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            text-align: center;
            box-sizing: border-box;
        }

        select, input[type="text"], input[type="url"] {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            border: none;
            font-size: 1rem;
            color: #fff;
            background-color: rgba(0, 0, 0, 0.4);
            /* To make text visible inside inputs */
        }

        select option {
            color: #000; /* Make dropdown options readable */
        }

        button {
            width: 100%;
            padding: 12px;
            background: #00ffcc;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            color: #000;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #00d1a8;
        }

        .manage-songs-box {
            display: inline-block;
            padding: 15px 30px;
            margin-top: 25px;
            background: #6a5af9;
            color: white;
            font-weight: bold;
            font-size: 1rem;
            text-align: center;
            text-decoration: none;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(106, 90, 249, 0.3);
            transition: background-color 0.3s ease;
        }

        .manage-songs-box:hover {
            background-color: #5749d9;
        }
        .back-link{
            color:white;
            font-size:18px;
            position:absolute;
            bottom: 120px;px;
            center:60px;
        }
        .back-link:hover{
            text-decoration:underline;
            
        }
    </style>
</head>
<body>
    <a href="admin_dashboard.php" class="back-link">⬅️Back to Dashboard</a>
<div class="form-box">
    <h2>➕ Add New Song</h2>

    <form method="post" autocomplete="off">
        <select name="mood" required>
            <option value="" disabled selected>Select Mood</option>
            <?php if (count($moods) > 0): ?>
                <?php foreach ($moods as $mood): ?>
                    <option value="<?= htmlspecialchars($mood) ?>"><?= ucfirst(htmlspecialchars($mood)) ?></option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="" disabled>No moods found. Add a song with a new mood first.</option>
            <?php endif; ?>
        </select>

        <input type="text" name="song_title" placeholder="Enter song title..." required>

        <input type="url" name="song_link" placeholder="Enter song link (URL)..." required>

        <button type="submit">Add Song</button>
    </form>

    <!-- 📋 Manage Songs Button -->
    <a href="admin_manage_song.php" class="manage-songs-box">📋 Manage Songs</a>
</div>
</body>
</html>
