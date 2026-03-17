<?php
// Basic router for plain PHP API
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../src/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = dirname($_SERVER['SCRIPT_NAME']);

// Normalize path relative to public directory
if (strpos($path, $scriptName) === 0) {
    $path = substr($path, strlen($scriptName));
}
$path = trim($path, '/');
$segments = explode('/', $path);

// Expect paths like: api/users, api/products, etc.
if (count($segments) < 2 || $segments[0] !== 'api') {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

$resource = $segments[1];
array_shift($segments); // remove 'api'
array_shift($segments); // remove resource

// Include route handler
$routeFile = __DIR__ . '/../src/routes/' . $resource . '.php';
if (!file_exists($routeFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Resource not found']);
    exit;
}

require_once $routeFile;

$handler = $resource . '_handler';
if (!function_exists($handler)) {
    http_response_code(500);
    echo json_encode(['error' => 'Handler missing']);
    exit;
}

$body = null;
$raw = file_get_contents('php://input');
if ($raw) {
    $body = json_decode($raw, true);
}

try {
    $handler($method, $segments, $body, $pdo);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'message' => $e->getMessage()]);
}

?>