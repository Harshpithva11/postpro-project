<?php
// Centralized Database Connection for Local (XAMPP) & Cloud (Vercel)

$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$dbname = getenv('DB_NAME') ?: "social";
$port = getenv('DB_PORT') ? intval(getenv('DB_PORT')) : 3306;

// 1. Initialize MySQLi
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect($host, $user, $pass, $dbname, $port);

// 2. Initialize PDO
$pdo = null;
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    // Handled below if needed
}

// 3. Graceful error handling for Cloud / Vercel when DB is not configured
if ((!$conn || $conn->connect_error) && !$pdo) {
    if (getenv('VERCEL') || ($host !== 'localhost' && $host !== '127.0.0.1')) {
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
                  || (isset($_GET['action'])) || (isset($_GET['mood']));
        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                "error" => "Database connection failed. Please configure DB_HOST, DB_USER, DB_PASS, DB_NAME in Vercel Environment Variables."
            ]);
            exit;
        } else {
            echo "<!DOCTYPE html><html><head><title>Database Configuration Required</title><meta name='viewport' content='width=device-width, initial-scale=1'><style>body{background:#0f172a;color:#f8fafc;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;box-sizing:border-box}.card{background:#1e293b;border:1px solid #334155;border-radius:16px;padding:32px;max-width:560px;width:100%;box-shadow:0 20px 40px rgba(0,0,0,0.4)}h2{color:#f43f5e;margin-top:0}code{background:#0f172a;padding:3px 8px;border-radius:6px;color:#38bdf8}ul{line-height:1.8}.back-btn{display:inline-block;margin-top:20px;background:#38bdf8;color:#0f172a;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600}</style></head><body><div class='card'><h2>⚠️ Cloud Database Not Connected</h2><p>Your Vercel site is running successfully, but it needs a cloud MySQL database connection.</p><p>Because Vercel is a serverless platform, <code>localhost</code> does not exist on Vercel.</p><p><strong>To connect your database:</strong></p><ul><li>Create a free MySQL database on <strong>TiDB Cloud</strong> or <strong>Aiven</strong>.</li><li>Import your <code>DataBase/social.sql</code> file into it.</li><li>In your Vercel Project Dashboard, go to <strong>Settings &rarr; Environment Variables</strong>.</li><li>Add <code>DB_HOST</code>, <code>DB_USER</code>, <code>DB_PASS</code>, <code>DB_NAME</code>, and <code>DB_PORT</code>.</li></ul><a href='postpro.php' class='back-btn'>&larr; Back to Home</a></div></body></html>";
            exit;
        }
    }
}
?>
