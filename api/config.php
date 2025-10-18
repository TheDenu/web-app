<?php
$host = "MySQL-8.0";
$user = "root";
$pass = "";
$dbname = "web_app_db";

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_errno) {
    require 'Controller/BaseController.php';
    $controller = new BaseController();
    $controller->sendServerError("Ошибка подключения к базе данных");
    exit();
}
