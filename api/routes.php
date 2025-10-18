<?php
require_once 'Controller/UserController.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === '/api/test'){
    $controller = new UserController();
    $controller->test();
}