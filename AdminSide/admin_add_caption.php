<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/admin_db.php';

// Handle caption submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $caption = trim($_POST["caption"]);
    $keyword = trim($_POST["keyword"]);

    if (!empty($caption) && !empty($keyword)) {
        $stmt = $conn->prepare("INSERT INTO captions (keyword, caption_text) VALUES (?, ?)");
        $stmt->bind_param("ss", $keyword, $caption);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('✅ Caption added successfully!');</script>";
    } else {
        echo "<script>alert('⚠️ All fields are required.');</script>";
    }
}

// Fetch unique moods (keywords) from the database
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
    <title>Add Caption</title>
    <style>
        body {
            background: #1e2a38;
            color: #fff;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .form-box {
            background: rgba(255,255,255,0.08);
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            text-align: center;
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

        .manage-captions-box {
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

        .manage-captions-box:hover {
            background-color: #5749d9;
        }
        .back-link{
            color:white;
            font-size:18px;
            position:absolute;
            bottom: 130px;px;
            center:60px;
        }
        .back-link:hover{
            text-decoration:underline;
           
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
    <a href="admin_dashboard.php" class="back-link">⬅️Back to Dashboard</a>
<div class="form-box">
    <h2>➕ Add New Caption</h2>

    <form method="post">
        <select name="keyword" required>
            <option value="">Select Mood</option>
            <?php foreach ($moods as $mood): ?>
                <option value="<?= htmlspecialchars($mood) ?>"><?= ucfirst(htmlspecialchars($mood)) ?></option>
            <?php endforeach; ?>
        </select>

        <textarea name="caption" placeholder="Enter caption here..." required></textarea>

        <button type="submit">Add Caption</button>
    </form>

    <!-- 📋 Manage Captions Button -->
    <a href="admin_manage_caption.php" class="manage-captions-box">📋 Manage Captions</a>
</div>
</body>
</html>
