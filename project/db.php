<?php
// Centralized High-Performance Database Connection for Local & Cloud (Vercel)

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');

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

// Detect which database driver is needed by the caller to avoid double SSL handshakes
$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
$caller = '';
foreach ($trace as $t) {
    if (!empty($t['file']) && basename($t['file']) !== 'db.php') {
        $caller = strtolower(basename($t['file']));
        break;
    }
}

$needsPdo = in_array($caller, ['caption.php', 'hashtag.php', 'fav.php', 'feedback.php']);
$needsMysqli = in_array($caller, ['song.php', 'uploadphoto.php', 'favorite.php', 'login_process.php', 'register_process.php', 'reset_process.php', 'admin_db.php']) || (strpos($caller, 'admin_') === 0);

// If caller is unknown (or called directly), connect both as fallback
if (!$needsPdo && !$needsMysqli) {
    $needsPdo = true;
    $needsMysqli = true;
}

// 1. Initialize MySQLi only when needed
$conn = null;
if ($needsMysqli) {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = mysqli_init();
    if ($conn) {
        if (file_exists($caCert)) {
            mysqli_ssl_set($conn, NULL, NULL, $caCert, NULL, NULL);
        }
        if (defined('MYSQLI_OPT_SSL_VERIFY_SERVER_CERT')) {
            mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
        }
        if (!@mysqli_real_connect($conn, $host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
            $conn = @mysqli_connect($host, $user, $pass, $dbname, $port);
        }
    }
}

// 2. Initialize PDO only when needed (PHP 8.5+ compatible)
$pdo = null;
if ($needsPdo) {
    $sslVerifyKey = defined('Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT') 
        ? constant('Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT') 
        : (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT') ? constant('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT') : 1014);

    $sslCaKey = defined('Pdo\Mysql::ATTR_SSL_CA') 
        ? constant('Pdo\Mysql::ATTR_SSL_CA') 
        : (defined('PDO::MYSQL_ATTR_SSL_CA') ? constant('PDO::MYSQL_ATTR_SSL_CA') : 1012);

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            $sslVerifyKey => false,
        ];
        if (file_exists($caCert)) {
            $options[$sslCaKey] = $caCert;
        }
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (Exception $e) {
        try {
            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (Exception $ex) {
            $pdo = null;
        }
    }
}
?>
