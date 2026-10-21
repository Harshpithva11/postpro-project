<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/admin_db.php';

// Handle GET (load existing song for editing)
$data = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM song_tbl WHERE song_id = $id");
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        die("❌ Song not found!");
    }
}

// Handle POST (update song)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $song_title = trim($_POST["song_title"]);
    $mood = trim($_POST["mood"]);
    $song_link = trim($_POST["song_link"]);
    $id = intval($_POST["id"]);

    if (!empty($song_title) && !empty($mood) && !empty($song_link)) {
        $stmt = $conn->prepare("UPDATE song_tbl SET song_title = ?, mood = ?, song_link = ? WHERE song_id = ?");
        $stmt->bind_param("sssi", $song_title, $mood, $song_link, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_manage_song.php");
        exit();
    } else {
        echo "<script>alert('⚠️ All fields are required.');</script>";
    }
}

// Fetch all distinct moods
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
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Song</title>
    <style>
        body {
            background: #1e2a38;
            color: #fff;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-box {
            background: rgba(255,255,255,0.08);
            padding: 30px;
            border-radius: 12px;
            width: 450px;
        }

        input[type="text"], select, textarea {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #00ffcc;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }

        a {
            color: #ccc;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        a:hover {
            text-decoration: underline;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: 600;
        }
    
@media (max-width: 768px) {
    body { margin: 10px !important; height: auto !important; min-height: 100vh !important; }
    .form-box { width: 95% !important; padding: 20px 14px !important; box-sizing: border-box !important; }
    table { width: 100% !important; display: block !important; overflow-x: auto !important; }
    th, td { padding: 8px 10px !important; font-size: 13px !important; }
}
</style>
</head>
<body>
<div class="form-box">
    <h2>✏️ Edit Song</h2>

    <?php if ($data): ?>
    <form method="post" novalidate>
        <input type="hidden" name="id" value="<?= htmlspecialchars($data['song_id']) ?>">

        <label for="song_title">Song Title</label>
        <input type="text" name="song_title" id="song_title" required value="<?= htmlspecialchars($data['song_title']) ?>">

        <label for="mood">Mood</label>
        <select name="mood" id="mood" required>
            <option value="">Select Mood</option>
            <?php foreach ($moods as $mood): ?>
                <option value="<?= htmlspecialchars($mood) ?>" <?= ($data['mood'] === $mood) ? 'selected' : '' ?>>
                    <?= ucfirst(htmlspecialchars($mood)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="song_link">Song Link (URL)</label>
        <textarea name="song_link" id="song_link" required><?= htmlspecialchars($data['song_link']) ?></textarea>

        <button type="submit">Update Song</button>
    </form>
    <?php else: ?>
        <p>⚠️ No song selected to edit.</p>
    <?php endif; ?>

    <a href="admin_manage_song.php">← Back to Songs</a>
</div>
</body>
</html>
