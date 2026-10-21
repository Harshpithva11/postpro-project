<?php
session_start(); // <-- Add session start to check login status

// song.php

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "social";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX request for songs by mood with pagination & random selection
if (isset($_GET['action']) && $_GET['action'] === 'get_songs' && isset($_GET['mood'])) {
    $mood = $_GET['mood'];
    $limit = 10; // songs per request
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

    $stmt = $conn->prepare("SELECT song_title, song_link FROM song_tbl WHERE mood = ? ORDER BY RAND() LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $mood, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $songs = [];
    while ($row = $result->fetch_assoc()) {
      $songs[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($songs);
    exit;
}

// Fetch distinct moods for dropdown
$moods = [];
$res = $conn->query("SELECT DISTINCT mood FROM song_tbl ORDER BY mood ASC");
if ($res) {
  while ($row = $res->fetch_assoc()) {
    $moods[] = $row['mood'];
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mood Songs</title>
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet" />
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
  <link rel="stylesheet" href="song.css" />
  <style>
    /* loader & song box styles unchanged */
    .loader {
      display: none;
      justify-content: center;
      align-items: center;
      margin: 20px 0;
    }
    .wave {
      background: #333;
      height: 20px;
      width: 5px;
      margin: 0 2px;
      animation: wave 1.2s infinite ease-in-out;
      display: inline-block;
    }
    .wave:nth-child(1) { animation-delay: -1.2s; }
    .wave:nth-child(2) { animation-delay: -1.1s; }
    .wave:nth-child(3) { animation-delay: -1.0s; }
    .wave:nth-child(4) { animation-delay: -0.9s; }
    .wave:nth-child(5) { animation-delay: -0.8s; }
    @keyframes wave {
      0%, 40%, 100% { transform: scaleY(0.4); }  
      20% { transform: scaleY(1.0); }
    }
    .song-box {
      border: 0px solid #ccc;
      padding: 10px;
      margin-bottom: 8px;
      border-radius: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .song-text {
      font-size: 16px;
    }
    .buttons button {
      margin-left: 6px;
      cursor: pointer;
    }
    #loadMoreBtn {
      display: none;
      margin: 10px auto;
      padding: 10px 20px;
      cursor: pointer;
      background-color: #5a0fcf;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 16px;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <a href="postpro.php">🏠 Homepage</a>
    <a href="caption.php">📝 Caption Page</a>
    <a href="hashtag.php">#️⃣ Hashtag Page</a>
    <a href="song.php" class="active">🎵 Song Page</a>
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
    <h2>★ Choose Your Mood & Get Songs</h2>
    
    <!-- Dropdown + Button -->
    <div class="mood-box">
      <select id="moodSelect" placeholder="-- Select Mood --">
        <option value="" selected disabled>-- Select Mood --</option>
        <?php foreach ($moods as $m): ?>
          <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
        <?php endforeach; ?>
      </select>
      <button onclick="suggestSongs(true)">Generate Songs</button>
    </div>

    <!-- Loader -->
    <div id="loader" class="loader">
      <div class="wave"></div>
      <div class="wave"></div>
      <div class="wave"></div>
      <div class="wave"></div>
      <div class="wave"></div>
    </div>

    <div id="songsList"></div>

    <!-- Load More button -->
    <button id="loadMoreBtn" onclick="loadMoreSongs()">Load More</button>
  </div>

  <!-- Tom Select JS -->
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

  <script>
    new TomSelect("#moodSelect", { create: false, sortField: { field: "text", direction: "asc" } });

    let currentMood = null;
    let offset = 0;
    const limit = 10;

    function suggestSongs(reset = false) {
      const mood = document.getElementById("moodSelect").value;
      const list = document.getElementById("songsList");
      const loader = document.getElementById("loader");
      const loadMoreBtn = document.getElementById("loadMoreBtn");

      if (!mood) {
        alert("Please select a mood.");
        return;
      }

      if (reset) {
        offset = 0;
        list.innerHTML = "";
        loadMoreBtn.style.display = "none";
      }

      currentMood = mood;
      loader.style.display = "flex";
      loadMoreBtn.disabled = true;

      fetch(`song.php?action=get_songs&mood=${encodeURIComponent(mood)}&offset=${offset}`)
        .then(res => res.json())
        .then(songs => {
          loader.style.display = "none";
          loadMoreBtn.disabled = false;

          if (songs.length === 0 && offset === 0) {
            list.innerHTML = "<p>No songs found for this mood.</p>";
            return;
          }

          songs.forEach(song => {
            const div = document.createElement("div");
            div.className = "song-box";

            const text = document.createElement("div");
            text.className = "song-text";
            text.innerText = song.song_title;

            const btns = document.createElement("div");
            btns.className = "buttons";

            // Play Button
            const playBtn = document.createElement("button");
            playBtn.className = "play-btn";
            playBtn.innerText = "▶ Play";
            playBtn.onclick = () => {
              window.open(song.song_link, "_blank");
            };

            // Copy Button
            const copyBtn = document.createElement("button");
            copyBtn.className = "copy-btn";
            copyBtn.innerText = "Copy 🗍";
            copyBtn.onclick = () => {
              navigator.clipboard.writeText(song.song_title + " - " + song.song_link);
              alert("Copied: " + song.song_title);
            };

            // Fav Button
            const favBtn = document.createElement("button");
            favBtn.className = "fav-btn";
            favBtn.innerHTML = 'Fav <span>♥</span>';
            favBtn.onclick = () => {
              <?php if (!isset($_SESSION['user_id'])): ?>
                alert('Please login to favorite songs.');
              <?php else: ?>
                fetch("fav.php?add=" + encodeURIComponent(song.song_title) + "&type=songs")
                  .then(() => {
                    alert(song.song_title + " added to Favorites ♥");
                  })
                  .catch(() => {
                    alert("Failed to add favorite.");
                  });
              <?php endif; ?>
            };

            btns.appendChild(playBtn);
            btns.appendChild(copyBtn);
            btns.appendChild(favBtn);

            div.appendChild(text);
            div.appendChild(btns);
            list.appendChild(div);
          });

          if (songs.length === limit) {
            loadMoreBtn.style.display = "block";
          } else {
            loadMoreBtn.style.display = "none";
          }

          offset += songs.length;
        })
        .catch(err => {
          loader.style.display = "none";
          list.innerHTML = "<p>Error loading songs. Try again later.</p>";
          console.error(err);
        });
    }

    function loadMoreSongs() {
      suggestSongs(false);
    }
  </script>
</body>
</html>
