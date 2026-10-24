<?php
/**
 * Vercel Serverless Gateway Router
 * Roppets Edutainment Production Inc.
 */

// Parse requested URI path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedPath = parse_url($requestUri, PHP_URL_PATH);
$path = '/' . trim($parsedPath, '/');

// Fail-safe query string population for serverless environments
if (empty($_GET) && !empty($parsedQuery = parse_url($requestUri, PHP_URL_QUERY))) {
    parse_str($parsedQuery, $_GET);
}

$rootDir = realpath(__DIR__ . '/..');

/**
 * Helper to resolve PHP file (with or without .php extension)
 */
function resolvePhpFile($dir, $filename) {
    if (empty($filename)) return null;
    $target = $dir . '/' . $filename;
    if (is_file($target)) return $target;
    if (is_file($target . '.php')) return $target . '.php';
    return null;
}

// 1. Root index
if ($path === '/' || $path === '/index.php' || $path === '/index') {
    chdir($rootDir);
    require $rootDir . '/index.php';
    exit;
}

// 2. Admin routes (/admin, /admin/, /admin/login, /admin/bookings.php, etc.)
if (strpos($path, '/admin') === 0) {
    $subPath = trim(substr($path, strlen('/admin')), '/');
    $adminDir = $rootDir . '/admin';
    if (empty($subPath) || $subPath === 'index' || $subPath === 'index.php') {
        chdir($adminDir);
        require $adminDir . '/index.php';
        exit;
    }
    $resolved = resolvePhpFile($adminDir, $subPath);
    if ($resolved) {
        chdir($adminDir);
        require $resolved;
        exit;
    }
}

// 3. API routes (/api/book.php, /api/check-availability, etc.)
if (strpos($path, '/api') === 0) {
    $subPath = trim(substr($path, strlen('/api')), '/');
    $apiDir = __DIR__;
    if ($subPath !== 'index.php' && $subPath !== 'index' && !empty($subPath)) {
        $resolved = resolvePhpFile($apiDir, $subPath);
        if ($resolved) {
            chdir($apiDir);
            require $resolved;
            exit;
        }
    }
}

// 4. Root PHP pages (/services, /packages, /contact.php, etc.)
$subPath = trim($path, '/');
$resolved = resolvePhpFile($rootDir, $subPath);
if ($resolved) {
    chdir($rootDir);
    require $resolved;
    exit;
}

// 5. Static asset fallback (if routed through serverless)
$staticFile = $rootDir . '/' . ltrim($path, '/');
if (is_file($staticFile)) {
    $ext = strtolower(pathinfo($staticFile, PATHINFO_EXTENSION));
    $mimeTypes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'webp'  => 'image/webp',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'pdf'   => 'application/pdf',
        'json'  => 'application/json'
    ];
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
    }
    readfile($staticFile);
    exit;
}

// 6. 404 Not Found
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - Roppets Edutainment Production Inc.</title>
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; text-align: center; padding: 60px 20px; background: #f8fafc; color: #1e293b; }
        .box { max-width: 500px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        h1 { color: #e11d48; margin-bottom: 8px; font-size: 3rem; }
        p { color: #64748b; margin-bottom: 24px; }
        a { display: inline-block; background: #e11d48; color: #fff; text-decoration: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; }
        a:hover { background: #be123c; }
    </style>
</head>
<body>
    <div class="box">
        <h1>404</h1>
        <p>The page you are looking for could not be found.</p>
        <a href="/">Back to Home</a>
    </div>
</body>
</html>
