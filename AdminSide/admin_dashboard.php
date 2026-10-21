<?php
session_start();

if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <title>Admin Dashboard - POSTPRO</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #2e2f57, #0f2027); /* 🔥 Premium deep color */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .dashboard-container {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            text-align: center;
            color: #fff;
            max-width: 600px;
            width: 90%;
        }

        h2 {
            margin-bottom: 30px;
            font-size: 30px;
            color: #ffffffdd;
        }

        .admin-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
        }

        .admin-button {
            background-color: rgba(255, 255, 255, 0.15);
            padding: 18px 24px;
            border-radius: 14px;
            text-decoration: none;
            color: #fff;
            font-size: 17px;
            min-width: 180px;
            font-weight: 500;
            transition: 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .admin-button:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .logout-link {
            margin-top: 40px;
            display: inline-block;
            color: #ff4d4d;
            font-weight: bold;
            text-decoration: none;
        }

        .logout-link:hover {
            text-decoration: underline;
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
        @media (max-width: 600px) {
            body {
                height: auto !important;
                min-height: 100vh !important;
                padding: 20px 10px !important;
            }
            .dashboard-container {
                padding: 24px 16px !important;
                width: 95% !important;
            }
            .admin-button {
                min-width: 100% !important;
                font-size: 15px !important;
                padding: 14px 18px !important;
            }
        }
    </style>
</head>

<body>
    
    <div class="dashboard-container">
        <h2>👨‍💻 Admin Dashboard</h2>

        <div class="admin-links">
            <a href="admin_add_caption.php" class="admin-button">💬 Add Captions</a>
            <a href="admin_add_hashtag.php" class="admin-button">#️⃣ Add Hashtags</a>
            <a href="admin_add_song.php" class="admin-button">🎵 Add Songs</a>
        </div>
        

        <a href="admin_logout.php" class="logout-link">🚪 Logout</a>
       
    </div>
    
</body>

</html>
