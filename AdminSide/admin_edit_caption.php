<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/admin_db.php';

// Handle GET (load existing caption for editing)
$data = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM captions WHERE caption_id = $id");
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        die("❌ Caption not found!");
    }
}

// Handle POST (update caption)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $caption = trim($_POST["caption"]);
    $keyword = trim($_POST["keyword"]);
    $id = intval($_POST["id"]);

    if (!empty($caption) && !empty($keyword)) {
        $stmt = $conn->prepare("UPDATE captions SET keyword = ?, caption_text = ? WHERE caption_id = ?");
        $stmt->bind_param("ssi", $keyword, $caption, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_manage_caption.php");
        exit();
    } else {
        echo "<script>alert('⚠️ All fields are required.');</script>";
    }
}

// Fetch all distinct moods
$moods = [];
$result = $conn->query("SELECT DISTINCT keyword FROM captions ORDER BY keyword ASC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $moods[] = $row['keyword'];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Caption</title>
    <style>
        body {
            background: #1e2a38;
            color: #fff;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-box {
            background: rgba(255,255,255,0.08);
            padding: 30px;
            border-radius: 12px;
            width: 400px;
        }

        select, textarea {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            border: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #00ffcc;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
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
    <h2>✏️ Edit Caption</h2>

    <?php if ($data): ?>
    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($data['caption_id']) ?>">

        <select name="keyword" required>
            <option value="">Select Mood</option>
            <?php foreach ($moods as $mood): ?>
                <option value="<?= htmlspecialchars($mood) ?>" <?= ($data['keyword'] === $mood) ? 'selected' : '' ?>>
                    <?= ucfirst(htmlspecialchars($mood)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <textarea name="caption" required><?= htmlspecialchars($data['caption_text']) ?></textarea>

        <button type="submit">Update Caption</button>
    </form>
    <?php else: ?>
        <p>⚠️ No caption selected to edit.</p>
    <?php endif; ?>

    <a href="admin_manage_caption.php">← Back to Captions</a>
</div>
</body>
</html>
