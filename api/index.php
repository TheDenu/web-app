<?php
session_start();
require_once 'Service/DBConnect.php';
require_once 'Middleware/CorsMiddleware.php';
require_once 'Router.php';

$cors = new CorsMiddleware();
$cors->handle();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];
$key = "rate_$ip_" . date('Y-m-d-H');
$_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
if ($_SESSION[$key] > 300) {
    http_response_code(429);
    echo json_encode(['error' => 'Превышен лимит запросов']);
    exit;
}

try {
    $mysqli = getDBConnection();
} catch (Exception $e) {
    exit();
}

$router = new Router($mysqli);
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
