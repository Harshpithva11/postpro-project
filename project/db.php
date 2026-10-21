<?php
// Centralized Database Connection for Local & Cloud (Vercel)

$host = getenv('DB_HOST') ?: "gateway01.ap-northeast-1.prod.aws.tidbcloud.com";
$user = getenv('DB_USER') ?: "41YGiq3gNmStVD6.root";
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "2wklpg3iqU0Fdbl9";
$dbname = getenv('DB_NAME') ?: "social";
$port = getenv('DB_PORT') ? intval(getenv('DB_PORT')) : 4000;

// CA cert for SSL/TLS connections
$caCert = __DIR__ . '/ca.pem';
if (!file_exists($caCert)) {
    $caCert = '/etc/ssl/certs/ca-certificates.crt';
}

// 1. Initialize MySQLi with SSL
mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_init();
if ($conn) {
    if (file_exists($caCert)) {
        mysqli_ssl_set($conn, NULL, NULL, $caCert, NULL, NULL);
    }
    mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
    if (!@mysqli_real_connect($conn, $host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
        // Fallback procedural connect if local without SSL
        $conn = @mysqli_connect($host, $user, $pass, $dbname, $port);
    }
}

// 2. Initialize PDO with SSL
$pdo = null;
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
    if (file_exists($caCert)) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $caCert;
    }
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    // If SSL connection fails, try without SSL (e.g. for localhost)
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Exception $ex) {
        $pdo = null;
    }
}
?>
