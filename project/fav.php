<?php
session_start();

require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Login required']);
    exit;
}

if (isset($_GET['add'], $_GET['type'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $type = $_GET['type'];
    $text = $_GET['add'];

    // Initialize all text fields to NULL
    $caption_text = null;
    $hashtag_text = null;
    $song_text = null;

    // Assign text to proper column based on type
    if ($type === 'captions') {
        $caption_text = $text;
        $type_db = 'caption';
    } elseif ($type === 'hashtags') {
        $hashtag_text = $text;
        $type_db = 'hashtag';
    } elseif ($type === 'songs') {
        $song_text = $text;
        $type_db = 'song';
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid type']);
        exit;
    }

    $sql = "INSERT INTO favorites (user_id, username, type, caption_text, hashtag_text, song_text)
            VALUES (:user_id, :username, :type, :caption_text, :hashtag_text, :song_text)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':type', $type_db, PDO::PARAM_STR);
    $stmt->bindParam(':caption_text', $caption_text, PDO::PARAM_STR);
    $stmt->bindParam(':hashtag_text', $hashtag_text, PDO::PARAM_STR);
    $stmt->bindParam(':song_text', $song_text, PDO::PARAM_STR);

    try {
        $stmt->execute();
        echo json_encode(['success' => 'Added to favorites']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add favorite: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
}
