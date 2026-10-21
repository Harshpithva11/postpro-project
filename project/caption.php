<?php
session_start();

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

require_once __DIR__ . '/db.php';

// AJAX request handler
if (isset($_GET['mood'])) {
    $mood = $_GET['mood'];
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;

    $sql = "SELECT caption_text FROM captions WHERE keyword = :mood ORDER BY RAND() LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':mood', $mood, PDO::PARAM_STR);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $captions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($captions);
    exit;
}

// Fetch moods for dropdown
$sql = "SELECT DISTINCT keyword FROM captions ORDER BY keyword ASC";
$stmt = $pdo->query($sql);
$keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Mood Captions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet" />
  <link rel="stylesheet" href="caption.css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

  <div class="navbar">
    <a href="postpro.php">🏠 Homepage</a>
    <a href="caption.php" class="active">📝 Caption Page</a>
    <a href="hashtag.php">#️⃣ Hashtag Page</a>
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
    <h2>★ Choose Your Mood & Get Captions</h2>

    <div class="mood-box">
      <select id="moodSelect" placeholder="-- Select Mood --">
        <option value="" selected disabled>-- Select Mood --</option>
        <?php foreach ($keywords as $keyword): ?>
            <option value="<?= htmlspecialchars($keyword['keyword']) ?>"><?= htmlspecialchars($keyword['keyword']) ?></option>
        <?php endforeach; ?>
      </select>
      <button onclick="startLoading()">Generate Captions</button>
    </div>

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

  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

  <script>
    new TomSelect("#moodSelect", {
      create: false,
      sortField: { field: "text", direction: "asc" }
    });

    let offset = 0;
    const limit = 10;
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

      fetch(`caption.php?mood=${encodeURIComponent(mood)}&offset=${offsetVal}&limit=${limitVal}`)
        .then(res => res.json())
        .then(data => {
          loader.style.display = "none";

          if (!data || data.length === 0) {
            if (offsetVal === 0) {
              list.innerHTML = '<p>No captions found for this mood.</p>';
            }
            loadMoreBtn.style.display = 'none';
            return;
          }

          data.forEach(item => {
            const div = document.createElement("div");
            div.className = "caption-box";

            const text = document.createElement("div");
            text.className = "caption-text";
            text.innerText = item.caption_text;

            const btns = document.createElement("div");
            btns.className = "buttons";

            // Copy Button
            const copyBtn = document.createElement("button");
            copyBtn.className = "copy-btn";
            copyBtn.innerHTML = 'Copy <i class="fa-regular fa-copy"></i>';
            copyBtn.onclick = () => {
              navigator.clipboard.writeText(item.caption_text);
              alert("Copied to clipboard ✔");
            };

            // Favorite Button
            const favBtn = document.createElement("button");
            favBtn.className = "fav-btn";
            favBtn.innerHTML = 'Fav <span>♥</span>';
            favBtn.onclick = () => {
              fetch("fav.php?add=" + encodeURIComponent(item.caption_text) + "&type=captions")
                .then(res => {
                  if (!res.ok) {
                    if (res.status === 401) {
                      alert("Please login to add favorites.");
                      return;
                    }
                    throw new Error('Failed to add favorite');
                  }
                  return res.json();
                })
                .then(data => {
                  if (data && data.success) {
                    alert("Added to Favorites ♥");
                  } else if (data && data.error) {
                    alert("Error: " + data.error);
                  }
                })
                .catch(err => alert(err.message));
            };

            btns.appendChild(copyBtn);
            btns.appendChild(favBtn);
            div.appendChild(text);
            div.appendChild(btns);
            list.appendChild(div);
          });

          loadMoreBtn.style.display = (data.length === limitVal) ? 'inline-block' : 'none';
        })
        .catch(err => {
          loader.style.display = "none";
          list.innerHTML = `<p style="color:red;">Error loading captions. ${err.message}</p>`;
          loadMoreBtn.style.display = 'none';
        });
    }
  </script>
</body>
</html>
