<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "social");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --- Function to fetch hashtags ---
function fetch_hashtags($conn, $mood, $limit = 5)
{
    $hashtags = [];
    $stmt = $conn->prepare("SELECT hashtag_text FROM hashtags WHERE LOWER(mood)=LOWER(?) ORDER BY RAND() LIMIT ?");
    $stmt->bind_param("si", $mood, $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $hashtags[] = $r['hashtag_text'];
    $stmt->close();
    return $hashtags;
}

// --- Shuffle APIs ---
if (isset($_GET['action'], $_GET['mood'])) {
    header('Content-Type: application/json');
    $mood = $_GET['mood'];

    if ($_GET['action'] === 'shuffle_caption') {
        $stmt = $conn->prepare("SELECT caption_text FROM captions WHERE keyword=? ORDER BY RAND() LIMIT 1");
        $stmt->bind_param("s", $mood);
        $stmt->execute();
        $stmt->bind_result($caption);
        $stmt->fetch();
        $stmt->close();
        echo json_encode(['caption' => $caption ?: 'No caption found']);
        exit;
    }

    if ($_GET['action'] === 'shuffle_hashtag') {
        echo json_encode(['hashtags' => fetch_hashtags($conn, $mood, 5)]);
        exit;
    }

    if ($_GET['action'] === 'shuffle_song') {
        $stmt = $conn->prepare("SELECT song_title,song_link FROM song_tbl WHERE mood=? ORDER BY RAND() LIMIT 1");
        $stmt->bind_param("s", $mood);
        $stmt->execute();
        $stmt->bind_result($song, $song_link);
        $stmt->fetch();
        $stmt->close();
        echo json_encode(['song' => $song ?: 'No song found', 'song_link' => $song_link]);
        exit;
    }
}


// --- Main Generation ---
$moods = [];
$caption = "";
$hashtags = [];
$song = "";
$song_link = "";
$uploadedPhotoPath = "";
$errorMsg = "";

// Fetch moods for dropdown
$res = $conn->query("SELECT DISTINCT mood FROM song_tbl");
while ($row = $res->fetch_assoc()) $moods[] = $row['mood'];
$res->free();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mood = $_POST['mood'] ?? "";

    if (!$mood) {
        $errorMsg = "Select mood";
    } elseif (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== 0) {
        $errorMsg = "Upload photo";
    } else {
        $dir = "uploads/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["photo"]["name"]);
        $target = $dir . $fileName;
        $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errorMsg = "Invalid file type";
        } elseif (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target)) {
            $errorMsg = "Upload error";
        } else {
            $uploadedPhotoPath = $target;
        }
    }

    if (!$errorMsg) {
        // Caption
        $stmt = $conn->prepare("SELECT caption_text FROM captions WHERE keyword=? ORDER BY RAND() LIMIT 1");
        $stmt->bind_param("s", $mood);
        $stmt->execute();
        $stmt->bind_result($caption);
        $stmt->fetch();
        $stmt->close();

        // Hashtags
        $hashtags = fetch_hashtags($conn, $mood, 5);

        // Song
        $stmt = $conn->prepare("SELECT song_title,song_link FROM song_tbl WHERE mood=? ORDER BY RAND() LIMIT 1");
        $stmt->bind_param("s", $mood);
        $stmt->execute();
        $stmt->bind_result($song, $song_link);
        $stmt->fetch();
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mood Post</title>
<link rel="stylesheet" href="uploadphoto.css" />

 <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

<!-- 🌟 Glassmorphism Navbar -->
  <div class="navbar">
    <a href="postpro.php">🏠 Homepage</a>
    <a href="caption.php">📝 Caption Page</a>
    <a href="hashtag.php">#️⃣ Hashtag Page</a>
    <a href="song.php">🎵 Song Page</a>
    <a href="uploadphoto.php" class="active">📸 PicVibezz</a>
    <a href="favorite.php">⭐ Favorites</a>
    
    <!-- Add Login Icon -->
    <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php"><i class="fa fa-sign-in-alt"></i>Login</a>
    <?php else: ?>
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i>Logout</a>
    <?php endif; ?>
  </div>

<h2>🎭 Generate Mood Post</h2>

<form method="post" enctype="multipart/form-data" class="form-container">
    <select name="mood" id="mood" required>
        <option value="">-- Select Mood --</option>
        <?php foreach ($moods as $m): ?>
            <option value="<?= htmlspecialchars($m) ?>" <?= (isset($mood) && $mood == $m) ? 'selected' : ''; ?>>
                <?= htmlspecialchars(ucfirst($m)) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div class="upload-box">
        <label for="photoUpload" style="cursor:pointer; display:flex; justify-content:center; align-items:center; border:2px dashed #aaa; padding:20px; border-radius:10px;">
            Click to upload photo
            <input type="file" name="photo" id="photoUpload" accept="image/*" style="display:none;" onchange="previewImage(event)" />
        </label>
    </div>

    <button type="submit">Generate ✨</button>
</form>

<?php if ($errorMsg): ?>
    <div class="error-message"><?= htmlspecialchars($errorMsg) ?></div>
<?php endif; ?>

<img id="previewImg" style="display:none; margin-top:20px; max-width:400px; border-radius:12px;" alt="Preview" />

<?php if ($uploadedPhotoPath && !$errorMsg): ?>
    <div class="posted-image-container">
        <img src="<?= htmlspecialchars($uploadedPhotoPath) ?>" alt="Uploaded Image" />
    </div>

    <div id="resultBoxes">
        <!-- Caption -->
        <div class="suggestion-box">
            <div class="box-header">
                Caption
                <button class="shuffle-btn" onclick="shuffleField('caption')">Generate Another</button>
            </div>
            <div class="box-content" id="captionText"><?= htmlspecialchars($caption) ?></div>
            <div class="box-footer">
                <button class="copy-btn" onclick="copyText('captionText')">Copy</button>
            </div>
        </div>

        <!-- Hashtags -->
        <div class="suggestion-box">
            <div class="box-header">
                Hashtags
                <button class="shuffle-btn" onclick="shuffleField('hashtag')">Generate Another</button>
            </div>
            <div class="box-content" id="hashtagText"><?= '#' . implode(' #', array_map(fn ($t) => ltrim($t, '#'), $hashtags)) ?></div>
            <div class="box-footer">
                <button class="copy-btn" onclick="copyText('hashtagText')">Copy</button>
            </div>
        </div>

        <!-- Song -->
        <div class="suggestion-box">
            <div class="box-header">
                Song
                <button class="shuffle-btn" onclick="shuffleField('song')">Generate Another</button>
            </div>
            <div class="box-content" id="songText">
                <?php if ($song_link): ?>
                    <a href="<?= htmlspecialchars($song_link) ?>" target="_blank"><?= htmlspecialchars($song) ?></a>
                <?php else: ?>
                    <?= htmlspecialchars($song) ?>
                <?php endif; ?>
            </div>
            <div class="box-footer">
                <button class="copy-btn" onclick="copyText('songText')">Copy</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function copyText(id) {
  let el = document.getElementById(id);
  let text = el.innerText || el.textContent;
  let a = el.querySelector('a');
  if (a) text = a.href;
  navigator.clipboard.writeText(text);
  alert('Copied!');
}


function shuffleField(type) {
  let mood = document.getElementById('mood').value;
  if (!mood) return alert('Select mood first.');

  fetch(`?action=shuffle_${type}&mood=${encodeURIComponent(mood)}`)
    .then(r => r.json())
    .then(d => {
      if (type === 'caption' && d.caption) {
        document.getElementById('captionText').textContent = d.caption;
      }

      if (type === 'hashtag' && d.hashtags) {
        document.getElementById('hashtagText').textContent =
          '#' + d.hashtags.map(h => h.replace(/^#/, '')).join(' #');
      }

      if (type === 'song' && d.song) {
        const songText = d.song_link
          ? `<a href="${d.song_link}" target="_blank">${d.song}</a>`
          : d.song;
        document.getElementById('songText').innerHTML = songText;
      }
    });
}

function previewImage(event) {
  const input = event.target;
  const preview = document.getElementById('previewImg');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    preview.style.display = 'none';
  }
}
</script>

</body>
</html>
