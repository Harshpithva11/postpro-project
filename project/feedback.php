<?php
// Database credentials
$host = "localhost";
$dbname = "social";
$username = "root";
$password = "";

$submitted = false;
$rating = $emoji = $comments = $email = "";
$errors = [];

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    if (!$email) {
        $errors[] = "Please enter a valid email address.";
    }

    // Rating is reversed: user selects star value 1-5, but we map 1 => 5 stars, 2 => 4 stars, etc.
    $rating_input = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    if ($rating_input < 1 || $rating_input > 5) {
        $errors[] = "Please select a rating between 1 and 5 stars.";
    } else {
        $rating = 6 - $rating_input;  // Reverse rating
    }

    $emoji = isset($_POST['emoji']) ? $_POST['emoji'] : '';
    if (!in_array($emoji, ['😃', '😐', '😞'])) {
        $errors[] = "Please select a valid emoji.";
    }

    $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
    if (empty($comments)) {
        $errors[] = "Please enter your comments.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO feedback_tbl (fd_email, fd_emoji, fd_description, fd_star, fd_created_at) 
            VALUES (:email, :emoji, :comments, :star, NOW())
        ");
        $stmt->execute([
            ':email' => $email,
            ':emoji' => $emoji,
            ':comments' => $comments,
            ':star' => $rating
        ]);
        $submitted = true;
    }
}

// Fetch all feedbacks
$stmt = $pdo->query("SELECT * FROM feedback_tbl ORDER BY fd_id DESC");
$all_feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Project Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #fbc2eb, #a6c1ee);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px;
            flex-wrap: wrap;
        }

        .top-links {
            width: 100%;
            display: flex;
            justify-content: center; /* center horizontally */
            gap: 40px; /* spacing between links */
            padding-top: 20px; /* some padding from top */
            margin-bottom: 30px; /* space below the entire links container */
            position: fixed;
            top: 10px;
            left: 0;
            z-index: 1000;
            background: transparent;
        }

        .top-links a {
            color: white;
            font-weight: 750;
            font-size: 1.3rem;
            background-image: linear-gradient(to right, #54b3d6, #54b3d6 50%, #ffffff 50%);
            background-size: 200% 100%;
            background-position: -100%;
            display: inline-block;
            padding: 5px 0;
            position: relative;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            transition: all 0.3s ease-in-out;
            text-decoration: underline;
        }

        .top-links a::before {
            content: '';
            background: #54b3d6;
            display: block;
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 3px;
            transition: all 0.3s ease-in-out;
            text-decoration: underline;
        }

        .top-links a:hover {
            background-position: 0;
        }

        .top-links a:hover::before {
            width: 100%;
        }

        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            margin-right: 30px;
            margin-top: 80px; /* to avoid overlap with fixed top-links */
        }
        h2 {
            text-align: center;
            color: #6b46c1;
        }
        form label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
        }
        input[type="email"], textarea {
            width: 100%;
            padding: 10px;
            border-radius: 12px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
        }
        button {
            background: #6b46c1;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            margin-top: 15px;
            transition: 0.3s;
        }
        button:hover {
            background: #805ad5;
        }
        .stars {
            display: flex;
            flex-direction: row-reverse; /* Left to right */
            justify-content: center;
            margin: 10px 0;
        }
        .stars input {
            display: none;
        }
        .stars label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
            transition: 0.3s;
        }
        .stars input:checked + label,
        .stars input:checked ~ label {
            color: #f6b93b;
        }
        .emojis {
            display: flex;
            justify-content: center;
            margin: 10px 0;
        }
        .emojis input {
            display: none;
        }
        .emojis label {
            font-size: 30px;
            margin: 0 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .emojis input:checked + label {
            transform: scale(1.3);
        }
        .thank-you {
            text-align: center;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .feedback-list {
            max-width: 450px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            padding: 30px 25px;
            overflow-y: auto;
            max-height: 80vh;
            margin-top: 80px; /* align with form margin */
        }
        .feedback-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .feedback-item:last-child {
            border-bottom: none;
        }
        .feedback-email {
            font-weight: bold;
            color: #6b46c1;
        }
        .feedback-meta {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }
        .feedback-comments {
            white-space: pre-wrap;
            font-style: italic;
            color: #444;
        }
    </style>
</head>
<body>

<div class="top-links">
    <a href="postpro.php">HomePage</a>
    <a href="aboutus.php">AboutUs</a>
</div>

<div class="container">
    <?php if ($submitted): ?>
        <div class="thank-you">
            <h2>Thank you for your feedback! 🎉</h2>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p><strong>Rating:</strong> <?= $rating ?> ★</p>
            <p><strong>Emoji:</strong> <?= htmlspecialchars($emoji) ?></p>
            <p><strong>Comments:</strong> <?= nl2br(htmlspecialchars($comments)) ?></p>
            <button onclick="window.location.href=''">Submit Another Feedback</button>
        </div>
    <?php else: ?>
        <form method="POST" action="">
            <h2>Give Your Feedback</h2>

            <?php if (!empty($errors)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <label for="email">Your Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label>Rate our AI Generator:</label>
            <div class="stars">
                <?php
                // Output stars reversed: first star is 5, second is 4, ... last is 1
                for ($i = 5; $i >= 1; $i--): 
                    // checked logic: $rating is reversed rating (1 to 5), map back for checked state
                    $checked = ($rating == $i) ? 'checked' : '';
                    $input_value = 6 - $i; // Reverse again for input value (1 to 5)
                ?>
                    <input type="radio" name="rating" id="star<?= $input_value ?>" value="<?= $input_value ?>" <?= $checked ?>>
                    <label for="star<?= $input_value ?>">★</label>
                <?php endfor; ?>
            </div>

            <label>How do you feel?</label>
            <div class="emojis">
                <?php 
                $emojis = ['😃', '😐', '😞'];
                foreach ($emojis as $idx => $emj): ?>
                    <input type="radio" name="emoji" id="emoji<?= $idx+1 ?>" value="<?= $emj ?>" <?= $emoji == $emj ? 'checked' : '' ?>>
                    <label for="emoji<?= $idx+1 ?>"><?= $emj ?></label>
                <?php endforeach; ?>
            </div>

            <label>Comments:</label>
            <textarea name="comments" required><?= htmlspecialchars($comments) ?></textarea>

            <button type="submit">Submit Feedback</button>
        </form>
    <?php endif; ?>
</div>

<div class="feedback-list">
    <h2>All Feedbacks</h2>
    <?php if (empty($all_feedbacks)): ?>
        <p>No feedback yet.</p>
    <?php else: ?>
        <?php foreach ($all_feedbacks as $fb): ?>
            <div class="feedback-item">
                <div class="feedback-email"><?= htmlspecialchars($fb['fd_email']) ?></div>
                <div class="feedback-meta">
                    <?= htmlspecialchars($fb['fd_created_at']) ?> | <?= htmlspecialchars($fb['fd_emoji']) ?> | Rating: <?= htmlspecialchars($fb['fd_star']) ?> ★
                </div>
                <div class="feedback-comments"><?= nl2br(htmlspecialchars($fb['fd_description'])) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
