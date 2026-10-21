<?php
session_start();

// ✅ 1. DB Connection
$conn = new mysqli("localhost", "root", "", "social");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ 2. Check login and alert without redirect
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to view your favorites.');</script>";
    $user_id = null;
} else {
    $user_id = $_SESSION['user_id'];
}

// ✅ 3. Handle Unfavorite Action
if ($user_id && isset($_GET['remove']) && isset($_GET['type'])) {
    $removeItem = $_GET['remove'];
    $removeType = $_GET['type'];

    // Clean and determine column
    $column = '';
    switch ($removeType) {
        case 'caption': $column = 'caption_text'; break;
        case 'hashtag': $column = 'hashtag_text'; break;
        case 'song': $column = 'song_text'; break;
        default: $column = ''; break;
    }

    if ($column) {
        $stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND type = ? AND $column = ?");
        $stmt->bind_param("iss", $user_id, $removeType, $removeItem);
        $stmt->execute();
    }

    // Redirect to preserve UI
    header("Location: favorite.php?tab=$removeType");
    exit();
}

// ✅ 4. Active Tab (caption/hashtag/song)
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'caption';

// ✅ 5. Get favorites from database
function getFavoritesFromDB($conn, $user_id, $type) {
    $column = '';
    switch ($type) {
        case 'caption': $column = 'caption_text'; break;
        case 'hashtag': $column = 'hashtag_text'; break;
        case 'song': $column = 'song_text'; break;
        default: return [];
    }

    $stmt = $conn->prepare("SELECT $column FROM favorites WHERE user_id = ? AND type = ? AND $column IS NOT NULL AND $column != ''");
    $stmt->bind_param("is", $user_id, $type);
    $stmt->execute();
    $result = $stmt->get_result();

    $favorites = [];
    while ($row = $result->fetch_assoc()) {
        $favorites[] = $row[$column];
    }
    return $favorites;
}

// ✅ 6. Show favorites
function showFavorites($conn, $user_id, $type, $title, $icon, $activeTab) {
    $activeClass = ($activeTab === $type) ? "active" : "";
    echo "<div class='fav-section $activeClass' id='section-$type'>";
    echo "<h2>$icon $title</h2>";

    if ($user_id === null) {
        echo "<p class='empty-msg'>You need to log in to view $title.</p>";
        echo "</div>";
        return;
    }

    $favorites = getFavoritesFromDB($conn, $user_id, $type);

    if (!empty($favorites)) {
        echo "<div class='fav-grid'>";
        foreach ($favorites as $fav) {
            $encodedText = urlencode($fav);
            $escapedText = htmlspecialchars($fav, ENT_QUOTES);
            echo "<div class='fav-card'>
                    <p class='fav-text'>$escapedText</p>
                    <div class='card-actions'>";

            if ($type === 'song') {
                echo "<button class='btn play' onclick=\"searchSong('$escapedText')\">🎵 Play</button>";
            }

            echo "<button class='btn copy' onclick=\"copyText('$escapedText')\">📋 Copy</button>";
            echo "<a class='btn remove' href='favorite.php?remove=$encodedText&type=$type&tab=$type'>🗑 Remove</a>";

            echo "</div></div>";
        }
        echo "</div>";
    } else {
        echo "<p class='empty-msg'>No $title yet ❤️</p>";
    }

    echo "</div>";
}
?>

<!-- HTML CONTINUES AS IS -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Favorites</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    /* YOUR CSS UNCHANGED */
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background: linear-gradient(135deg, #e0c3fc, #8ec5fc); color: #333; }
    .navbar {
      margin: 20px auto;
      padding: 12px 25px;
      display: flex;
      justify-content: center;
      gap: 40px;
      background: linear-gradient(90deg, #7f7fd5, #86a8e7, #91eae4);
      border-radius: 40px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      width: fit-content;
    }
    .navbar a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      font-size: 18px;
      padding: 8px 15px;
      border-radius: 25px;
      transition: 0.3s;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .navbar a:hover { background: rgba(255,255,255,0.2); }
    .navbar .active { background: rgba(255,255,255,0.35); color: #222; }

    h1 { text-align: center; font-size: 30px; margin: 20px 0; color: #222; }

    .fav-box {
      margin: 30px auto;
      max-width: 900px;
      background: white;
      padding: 25px;
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      text-align: center;
    }

    .fav-tabs {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-bottom: 20px;
    }
    .fav-btn {
      border: none;
      padding: 10px 20px;
      border-radius: 20px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      background: #6a11cb;
      color: white;
      transition: 0.3s;
    }
    .fav-btn:hover { background: #2575fc; }
    .fav-btn.active { background: #ffd43b; color: #333; }

    .fav-section { display: none; text-align: left; }
    .fav-section.active { display: block; }

    .fav-section h2 { font-size: 22px; margin-bottom: 15px; color: #444; }

    .fav-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
    }

    .fav-card {
      background: #f4f6ff;
      padding: 18px;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .fav-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 18px rgba(0,0,0,0.2);
    }

    .fav-text { font-size: 16px; color: #2c3e50; margin-bottom: 15px; word-wrap: break-word; }

    .card-actions {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }
    .btn {
      border: none;
      padding: 8px 14px;
      border-radius: 25px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 600;
      transition: 0.3s;
    }
    .copy { background: #27ae60; color: white; }
    .copy:hover { background: #1e874b; }
    .play { background: #2980b9; color: white; }
    .play:hover { background: #1c5a87; }
    .remove { background: #e74c3c; color: white; text-decoration: none; }
    .remove:hover { background: #c0392b; }

    .empty-msg {
      font-style: italic;
      color: #999;
      padding: 10px;
    }
  </style>
</head>
<body>
  <!-- Navigation -->
  <div class="navbar">
    <a href="postpro.php">🏠 Homepage</a>
    <a href="caption.php">📝 Caption Page</a>
    <a href="hashtag.php">#️⃣ Hashtag Page</a>
    <a href="song.php">🎵 Song Page</a>
    <a href="uploadphoto.php">📸 PicVibezz</a>
    <a href="favorite.php" class="active">⭐ Favorites</a>

    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="login.php"><i class="fa fa-sign-in-alt"></i> Login</a>
    <?php else: ?>
      <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    <?php endif; ?>
  </div>

  <h1>⭐ My Favorites</h1>

  <div class="fav-box">
    <div class="fav-tabs">
      <button class="fav-btn <?php echo ($activeTab==='caption'?'active':''); ?>" onclick="switchFav('caption')">📝 Captions</button>
      <button class="fav-btn <?php echo ($activeTab==='hashtag'?'active':''); ?>" onclick="switchFav('hashtag')">#️⃣ Hashtags</button>
      <button class="fav-btn <?php echo ($activeTab==='song'?'active':''); ?>" onclick="switchFav('song')">🎵 Songs</button>
    </div>

    <?php
      showFavorites($conn, $user_id, 'caption', 'Favourite Captions', '📝', $activeTab);
      showFavorites($conn, $user_id, 'hashtag', 'Favourite Hashtags', '#️⃣', $activeTab);
      showFavorites($conn, $user_id, 'song', 'Favourite Songs', '🎵', $activeTab);
    ?>
  </div>

  <script>
    function copyText(text) {
      navigator.clipboard.writeText(text);
      alert("Copied: " + text);
    }
    function searchSong(song) {
      window.open("https://www.youtube.com/results?search_query=" + encodeURIComponent(song), "_blank");
    }
    function switchFav(tab) {
      var url = new URL(window.location.href);
      url.searchParams.set('tab', tab);
      window.location.href = url.toString();
    }
  </script>
</body>
</html>
