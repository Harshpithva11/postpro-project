<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PostPro Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  
  <link rel="stylesheet" href="postpro.css" />
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">⚡</div>
    <ul>
      <li onclick="location.href='postpro.php'"><i class="fas fa-home"></i>Home</li>
      <li onclick="location.href='caption.php'"><i class="fas fa-pen"></i>Captions</li>
      <li onclick="location.href='hashtag.php'"><i class="fas fa-hashtag"></i>Hashtags</li>
      <li onclick="location.href='song.php'"><i class="fas fa-music"></i>Songs</li>
      <li onclick="location.href='Favorite.php'"><i class="fas fa-heart"></i>My Favourites</li>
<?php if (isset($_SESSION['user_id'])): ?>
           <li onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i><span>Logout</span></li>
           <?php endif; ?>
    </ul>
  </div>

  <!-- Main -->
  <div class="main">
    <center>
        <h1>Welcome Back, Creator 👋</h1>
        <p>Unleash your content with smart captions, hashtags & more!</p>
    </center>

    <div class="top-links">
        <!-- Add Login Icon -->
        <?php if (!isset($_SESSION['user_id'])): ?>
          <a href="login.php"><i class="fa fa-sign-in-alt"></i>Login</a>
        <?php else: ?>
          <a href="logout.php"><i class="fa fa-sign-out-alt"></i>Logout</a>
        <?php endif; ?>
        <a href="aboutus.php">About Us</a>
        <a href="feedback.php">FeedBack</a>
    </div>
    <div class="cards">

      <div class="card" onclick="location.href='caption.php'">
        <h3>➕ Generate Caption</h3>
        <p>AI-powered smart captions for any photo or mood.</p>
      </div>

      <div class="card" onclick="location.href='hashtag.php'">
        <h3>🏷️ Generate Hashtag</h3>
        <p>Smart, trendy tags to boost your reach and engagement.</p>
      </div>
      <div class="card" onclick="location.href='song.php'">
        <h3>🎵 Suggest Songs</h3>
        <p>Pick perfect tracks to match your reel or vibe.</p>
      </div>
    </div>

    <div class="form-box">
      <h2>🖼️ Upload an Image to Discover Your Mood-Based Caption, Hashtags & Song</h2>
      <button onclick="location.href='uploadphoto.php'">Generate Now ✨</button>
    </div>
</body>
</html>
