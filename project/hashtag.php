<?php
session_start();

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

require_once __DIR__ . '/db.php';

// ✅ AJAX request handler
if (isset($_GET['mood'])) {
    $mood = $_GET['mood'];
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 25; // fetch more so groups of 5 work

    $sql = "SELECT hashtag_text FROM hashtags WHERE mood = :mood ORDER BY RAND() LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':mood', $mood, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $hashtags = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($hashtags);
    exit;
}

// ✅ Fetch moods for dropdown
$sql = "SELECT DISTINCT mood FROM hashtags ORDER BY mood ASC";
$stmt = $pdo->query($sql);
$moods = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta charset="UTF-8">
  <title>Mood Hashtags</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
  <link rel="stylesheet" href="caption.css"> <!-- your existing CSS -->

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <style>
    .caption-box {
      background: #f9f9ff;
      border-radius: 15px;
      padding: 35px;
      margin: 10px;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
      width: 250px;
      display: inline-block;
      vertical-align: top;
    } 
    .caption-text {
      font-size: 16px;
      margin-bottom: 10px;
      white-space: pre-line;
    }
    .buttons {
      display: flex;
      justify-content: space-between;
    }
    .copy-btn, .fav-btn {
      border: none;
      padding: 5px 10px;
      border-radius: 8px;
      cursor: pointer;
    }
    .copy-btn {
      background: #4a90e2;
      color: white;
    }
    .fav-btn {
      background: #ff5a7d;
      color: white;
    }
    .mood-box {
      margin-bottom: 20px;
    }
    #captionsList {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
    }
  </style>
</head>
<body>

  <!-- 🌟 Navbar -->
  <div class="navbar">
    <a href="postpro.php">🏠 Homepage</a>
    <a href="caption.php">📝 Caption Page</a>
    <a href="hashtag.php" class="active">#️⃣ Hashtag Page</a>
    <a href="song.php">🎵 Song Page</a>
    <a href="uploadphoto.php">📸 PicVibezz</a>
    <a href="favorite.php">⭐ Favorites</a>

    <!-- Add Login Icon -->
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php"><i class="fa fa-sign-in-alt"></i>Login</a>
    <?php else: ?>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i>Logout</a>
    <?php endif; ?>
  </div>

  <div class="container">
    <h2>★ Choose Your Mood & Get Hashtags</h2>

    <!-- ✅ Dropdown -->
    <div class="mood-box">
      <select id="moodSelect" placeholder="-- Select Mood --">
        <option value="" selected disabled>-- Select Mood --</option>
        <?php foreach ($moods as $mood): ?>
            <option value="<?= htmlspecialchars($mood['mood']) ?>"><?= htmlspecialchars($mood['mood']) ?></option>
        <?php endforeach; ?>
      </select>
      <button onclick="startLoading()">Generate Hashtags</button>
    </div>

    <!-- 🔥 Loader -->
    <div id="loader" class="loader" style="display:none;">
      <div class="wave"></div>
      <div class="wave"></div>
      <div class="wave"></div>
      <div class="wave"></div>
      <div class="wave"></div>
    </div>

    <div id="captionsList"></div>
    <button id="loadMoreBtn" style="display:none;" onclick="loadMore()">Load More</button>
  </div>

  <!-- Tom Select -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

  <script>
    // Enhance dropdown
    new TomSelect("#moodSelect", {
      create: false,
      sortField: { field: "text", direction: "asc" }
    });

    let offset = 0;
    const limit = 25; // fetch more so groups of 5 work
    let currentMood = '';

    function startLoading() {
      offset = 0;
      currentMood = document.getElementById('moodSelect').value;
      document.getElementById('captionsList').innerHTML = '';
      if (!currentMood) {
        alert('Please select a mood first.');
        return;
      }
      fetchCaptions(currentMood, offset, limit);
    }

    function loadMore() {
      offset += limit;
      fetchCaptions(currentMood, offset, limit);
    }

    function fetchCaptions(mood, offsetVal, limitVal) {
      const loader = document.getElementById('loader');
      const list = document.getElementById('captionsList');
      const loadMoreBtn = document.getElementById('loadMoreBtn');

      loader.style.display = "flex";

      fetch(`hashtag.php?mood=${encodeURIComponent(mood)}&offset=${offsetVal}&limit=${limitVal}`)
        .then(res => res.json())
        .then(data => {
          loader.style.display = "none";

          if (!data || data.length === 0) {
            if (offsetVal === 0) {
              list.innerHTML = '<p>No hashtags found for this mood.</p>';
            }
            loadMoreBtn.style.display = 'none';
            return;
          }

          // ✅ Group hashtags in sets of 5
          for (let i = 0; i < data.length; i += 5) {
            const group = data.slice(i, i + 5);

            const div = document.createElement("div");
            div.className = "caption-box";

            const text = document.createElement("div");
            text.className = "caption-text";

            // Add hashtags inside one box
            text.innerHTML = group.map(item => item.hashtag_text).join("<br>");

            const btns = document.createElement("div");
            btns.className = "buttons";

            // Copy Button (copies all 5 hashtags)
            const copyBtn = document.createElement("button");
            copyBtn.className = "copy-btn";
            copyBtn.innerHTML = 'Copy <i class="fa-regular fa-copy"></i>';

            copyBtn.onclick = () => {
              const hashtagsToCopy = group.map(item => item.hashtag_text).join("\n");
              navigator.clipboard.writeText(hashtagsToCopy);
              alert("Copied to clipboard ✔");
            };

            // Favorite Button (saves all 5 hashtags as one group)
            const favBtn = document.createElement("button");
            favBtn.className = "fav-btn";
            favBtn.innerHTML = 'Fav <span>♥</span>';

            favBtn.onclick = () => {
              <?php if (!isset($_SESSION['user_id'])): ?>
                alert('Please login to favorite hashtags.');
              <?php else: ?>
                const hashtagsToSave = group.map(item => item.hashtag_text).join(", ");
                fetch("fav.php?add=" + encodeURIComponent(hashtagsToSave) + "&type=hashtags")
                  .then(res => res.json())
                  .then(data => {
                    if(data.success) alert("Added to Favorites ♥");
                    else alert("Failed: " + (data.error || "Unknown error"));
                  });
              <?php endif; ?>
            };

            btns.appendChild(copyBtn);
            btns.appendChild(favBtn);
            div.appendChild(text);
            div.appendChild(btns);
            list.appendChild(div);
          }

          loadMoreBtn.style.display = (data.length === limitVal) ? 'inline-block' : 'none';
        })
        .catch(err => {
          loader.style.display = "none";
          list.innerHTML = `<p style="color:red;">Error loading hashtags. ${err.message}</p>`;
          loadMoreBtn.style.display = 'none';
        });
    }
  </script>
</body>
</html>
