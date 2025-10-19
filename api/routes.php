<?php
require_once 'Service/DBConnect.php';
require_once 'Controller/UserController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$userController = new UserController($mysqli, $jwtService);

if ($uri === '/api/registration' && $method === 'POST') {
    $userController->registration();
} 
if ($uri === '/api/login' && $method === 'POST'){
    $userController->login();
}
