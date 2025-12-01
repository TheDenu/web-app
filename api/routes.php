<?php
require_once 'Service/DBConnect.php';
require_once 'vendor/autoload.php';
require_once 'Service/JwtService.php';
require_once 'Model/UserModel.php';
require_once 'Controller/UserController.php';
require_once 'Controller/ApplicationController.php';
require_once 'Middleware/AuthMiddleware.php';
require_once 'Middleware/CorsMiddleware.php';
$cors = new CorsMiddleware();
$cors->handle();

try {
    $mysqli = getDBConnection();
} catch (Exception $e) {
    exit();
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];


$jwtService = new JwtService();
$userModel = new UserModel($mysqli, $jwtService);
$userController = new UserController($mysqli, $jwtService);
$applicationController = new ApplicationController($mysqli);
$authMiddleware = new AuthMiddleware($mysqli);

switch ($uri) {
    case '/api/registration':
        if ($method === 'POST') {
            $userController->registration();
        }
        break;

    case '/api/login':
        if ($method === 'POST') {
            $userController->login();
        }
        break;

    case '/api/application/create':
        if ($method === 'POST') {
            $authMiddleware->handle(function () use ($applicationController) {
                $applicationController->createApplication();
            });
        }
        break;

    case '/api/application/list':
        if ($method === 'GET') {
            $authMiddleware->handle(function () use ($applicationController, $mysqli) {
                if (AuthMiddleware::isAdmin()) {
                    $applicationController->getAll();
                } else {
                    http_response_code(403);
                    echo json_encode(['error' => 'Только для администраторов']);
                }
            });
        }
        break;

    case '/api/application/my-list':
        if ($method === 'GET') {
            $authMiddleware->handle(function () use ($applicationController) {
                $applicationController->getMy();
            });
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Маршрут не найден']);
        break;
}
