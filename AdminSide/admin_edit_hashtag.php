<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/admin_db.php';

// Handle GET (load existing hashtag for editing)
$data = null;
if (isset($_GET['id'])) { 
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM hashtags WHERE hashtag_id = $id");
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        die("❌ Hashtag not found!");
    }
}

// Handle POST (update hashtag)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hashtag = trim($_POST["hashtag"]);
    $mood = trim($_POST["mood"]);
    $id = intval($_POST["id"]);

    if (!empty($hashtag) && !empty($mood)) {
        $stmt = $conn->prepare("UPDATE hashtags SET mood = ?, hashtag_text = ? WHERE hashtag_id = ?");
        $stmt->bind_param("ssi", $mood, $hashtag, $id);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_manage_hashtag.php");
        exit();
    } else {
        echo "<script>alert('⚠️ All fields are required.');</script>";
    }
}

// Fetch all distinct moods
$moods = [];
$result = $conn->query("SELECT DISTINCT mood FROM hashtags ORDER BY mood ASC");
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
    <title>Edit Hashtag</title>
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
    </style>
</head>
<body>
<div class="form-box">
    <h2>✏️ Edit Hashtag</h2>

    <?php if ($data): ?>
    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($data['hashtag_id']) ?>">

        <select name="mood" required>
            <option value="">Select Mood</option>
            <?php foreach ($moods as $mood): ?>
                <option value="<?= htmlspecialchars($mood) ?>" <?= ($data['mood'] === $mood) ? 'selected' : '' ?>>
                    <?= ucfirst(htmlspecialchars($mood)) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <textarea name="hashtag" required><?= htmlspecialchars($data['hashtag_text']) ?></textarea>

        <button type="submit">Update Hashtag</button>
    </form>
    <?php else: ?>
        <p>⚠️ No hashtag selected to edit.</p>
    <?php endif; ?>

    <a href="admin_manage_hashtag.php">← Back to Hashtags</a>
</div>
</body>
</html>
