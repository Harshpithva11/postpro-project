<?php
session_start();
if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}

require_once __DIR__ . '/admin_db.php';

// Handle hashtag submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hashtag = trim($_POST["hashtag"]);
    $mood = trim($_POST["mood"]);

    if (!empty($hashtag) && !empty($mood)) {
        $stmt = $conn->prepare("INSERT INTO hashtags (mood, hashtag_text) VALUES (?, ?)");
        $stmt->bind_param("ss", $mood, $hashtag);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('✅ Hashtag added successfully!');</script>";
    } else {
        echo "<script>alert('⚠️ All fields are required.');</script>";
    }
}

// Fetch unique moods from hashtags table
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
    <title>Add Hashtag</title>
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

        .manage-hashtags-box {
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

        .manage-hashtags-box:hover {
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
    </style>
</head>
<body>
     <a href="admin_dashboard.php" class="back-link">⬅️Back to Dashboard</a>
<div class="form-box">
    <h2>➕ Add New Hashtag</h2>

    <form method="post">
        <select name="mood" required>
            <option value="">Select Mood</option>
            <?php foreach ($moods as $mood): ?>
                <option value="<?= htmlspecialchars($mood) ?>"><?= ucfirst(htmlspecialchars($mood)) ?></option>
            <?php endforeach; ?>
        </select>

        <textarea name="hashtag" placeholder="Enter hashtag here..." required></textarea>

        <button type="submit">Add Hashtag</button>
    </form>

    <!-- 📋 Manage Hashtags Button -->
    <a href="admin_manage_hashtag.php" class="manage-hashtags-box">📋 Manage Hashtags</a>
</div>
</body>
</html>
