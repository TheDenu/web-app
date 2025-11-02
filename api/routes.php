<?php
require_once './vendor/autoload.php';
require_once './Service/JwtService.php';
require_once './Model/UserModel.php';
require_once 'Service/DBConnect.php';
require_once 'Service/AuthService.php';
require_once 'Controller/UserController.php';
require_once 'Controller/ApplicationController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$jwtService = new JwtService();
$userController = new UserController($mysqli, $jwtService);
$applicationController = new ApplicationController($mysqli);

if ($uri === '/api/registration' && $method === 'POST') {
    $userController->registration();
} 
if ($uri === '/api/login' && $method === 'POST'){
    $userController->login();
}
if($uri === 'api/application/create' && $method === 'POST'){
    //$authService->checkAuth();
    $applicationController->createApplication();
}
