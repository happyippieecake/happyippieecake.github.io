<?php
/**
 * Cloud Functions Entry Point
 * 
 * Entry point untuk Google Cloud Functions Gen2.
 * Routes all HTTP requests ke PHP file yang sesuai.
 */

use Google\CloudFunctions\FunctionsFramework;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

// Register the function with Functions Framework
FunctionsFramework::http('app', 'handleRequest');

/**
 * Handle incoming HTTP request
 */
function handleRequest(ServerRequestInterface $request): ResponseInterface
{
    // Get the URI path
    $uri = $request->getUri()->getPath();
    $path = trim($uri, '/');
    
    // Default to index.php
    if (empty($path)) {
        $path = 'index.php';
    }
    
    // Add .php extension if not present
    if (!str_ends_with($path, '.php') && !str_contains($path, '.')) {
        $path .= '.php';
    }
    
    // Check if it's a static file request
    $staticExtensions = ['css', 'js', 'ico', 'png', 'jpg', 'jpeg', 'gif', 'webp'];
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    
    if (in_array(strtolower($extension), $staticExtensions)) {
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
            $contentType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
            return new Response(200, ['Content-Type' => $contentType], file_get_contents($filePath));
        }
        return new Response(404, ['Content-Type' => 'text/plain'], 'File not found');
    }
    
    // Check if PHP file exists
    $targetFile = __DIR__ . '/' . $path;
    if (!file_exists($targetFile)) {
        return new Response(404, ['Content-Type' => 'text/html'], '<h1>404 Not Found</h1><p>File: ' . htmlspecialchars($path) . '</p>');
    }
    
    // Set up superglobals from PSR-7 request
    $_SERVER['REQUEST_METHOD'] = $request->getMethod();
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['QUERY_STRING'] = $request->getUri()->getQuery() ?? '';
    $_SERVER['HTTP_HOST'] = $request->getUri()->getHost();
    $_SERVER['HTTPS'] = 'on';
    $_GET = $request->getQueryParams();
    $_POST = (array) $request->getParsedBody();
    $_COOKIE = $request->getCookieParams();
    
    // Start output buffering and include the PHP file
    ob_start();
    
    try {
        // Change to the directory of the target file for relative includes
        $originalDir = getcwd();
        chdir(__DIR__);
        
        include $targetFile;
        
        chdir($originalDir);
        
        $output = ob_get_clean();
        return new Response(200, ['Content-Type' => 'text/html; charset=UTF-8'], $output);
        
    } catch (\Throwable $e) {
        ob_end_clean();
        error_log('Cloud Functions Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        return new Response(500, ['Content-Type' => 'text/html'], 
            '<h1>500 Internal Server Error</h1><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
    }
}
