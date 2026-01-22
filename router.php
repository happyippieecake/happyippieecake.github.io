<?php
/**
 * Cloud Functions Router
 * 
 * Entry point untuk Google Cloud Functions.
 * Routes all HTTP requests ke PHP file yang sesuai.
 */

use Google\CloudFunctions\FunctionsFramework;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

// Register the function with Functions Framework
FunctionsFramework::http('app', 'handleRequest');

function handleRequest(ServerRequestInterface $request): ResponseInterface
{
    // Get the URI path
    $uri = $request->getUri()->getPath();
    $path = ltrim($uri, '/');
    
    // Default to index.php
    if (empty($path) || $path === '/') {
        $path = 'index.php';
    }
    
    // Map routes to PHP files
    $routes = [
        '' => 'index.php',
        'index' => 'index.php',
        'index.php' => 'index.php',
        'pesan' => 'pesan.php',
        'pesan.php' => 'pesan.php',
        'login' => 'login.php',
        'login.php' => 'login.php',
        'admin' => 'admin.php',
        'admin.php' => 'admin.php',
        'dashboard' => 'dashboard.php',
        'dashboard.php' => 'dashboard.php',
        'payment' => 'payment.php',
        'payment.php' => 'payment.php',
        'payment_admin' => 'payment_admin.php',
        'payment_admin.php' => 'payment_admin.php',
        'data_pesanan' => 'data_pesanan.php',
        'data_pesanan.php' => 'data_pesanan.php',
        'edit_menu' => 'edit_menu.php',
        'edit_menu.php' => 'edit_menu.php',
        'export_laporan' => 'export_laporan.php',
        'export_laporan.php' => 'export_laporan.php',
        'check_orders' => 'check_orders.php',
        'check_orders.php' => 'check_orders.php',
        'db_connect' => 'db_connect.php',
        'db_connect.php' => 'db_connect.php',
        'PaymentGateway' => 'PaymentGateway.php',
        'PaymentGateway.php' => 'PaymentGateway.php',
    ];
    
    // Check if it's a static file request
    $staticExtensions = ['css', 'js', 'ico', 'png', 'jpg', 'jpeg', 'gif', 'webp'];
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    
    if (in_array($extension, $staticExtensions)) {
        $filePath = __DIR__ . '/' . $path;
        if (file_exists($filePath)) {
            $mimeTypes = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'ico' => 'image/x-icon',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
            return new Response(200, ['Content-Type' => $contentType], file_get_contents($filePath));
        }
    }
    
    // Find the target PHP file
    $targetFile = $routes[$path] ?? null;
    
    // If not in routes, check if the file exists directly
    if (!$targetFile && file_exists(__DIR__ . '/' . $path)) {
        $targetFile = $path;
    }
    
    if (!$targetFile || !file_exists(__DIR__ . '/' . $targetFile)) {
        return new Response(404, ['Content-Type' => 'text/html'], '<h1>404 Not Found</h1>');
    }
    
    // Set up superglobals from PSR-7 request
    $_SERVER['REQUEST_METHOD'] = $request->getMethod();
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['QUERY_STRING'] = $request->getUri()->getQuery();
    $_GET = $request->getQueryParams();
    $_POST = (array) $request->getParsedBody();
    
    // Parse cookies
    $cookies = $request->getCookieParams();
    foreach ($cookies as $key => $value) {
        $_COOKIE[$key] = $value;
    }
    
    // Start output buffering and include the PHP file
    ob_start();
    
    try {
        include __DIR__ . '/' . $targetFile;
        $output = ob_get_clean();
        
        // Get any headers that were set
        $headers = ['Content-Type' => 'text/html; charset=UTF-8'];
        
        return new Response(200, $headers, $output);
    } catch (Exception $e) {
        ob_end_clean();
        error_log('Error: ' . $e->getMessage());
        return new Response(500, ['Content-Type' => 'text/html'], '<h1>500 Internal Server Error</h1>');
    }
}
