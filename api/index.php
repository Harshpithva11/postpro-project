<?php
// Vercel Serverless PHP Router
ob_start();

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Redirect root requests to the project home page so relative links resolve correctly
if ($requestUri === '/' || $requestUri === '' || $requestUri === '/index.php') {
    header("Location: /project/postpro.php");
    exit();
}

$baseDir = dirname(__DIR__);
$filePath = realpath($baseDir . $requestUri);

// Security check: ensure path is within project root and is a PHP file
if ($filePath && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
    if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
        chdir(dirname($filePath));
        require $filePath;
        exit();
    }
}

// 404 handler
http_response_code(404);
echo "<h1>404 Not Found</h1>";
echo "<p>The requested file does not exist: " . htmlspecialchars($requestUri) . "</p>";
?>
