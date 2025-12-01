<?php
require_once './vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * Функция возвращает подключение к БД или завершает выполнение при ошибке
 */
function getDBConnection(): mysqli
{
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();

    $host = $_ENV['DB_HOST'];
    $user = $_ENV['DB_USER'];
    $pass = $_ENV['DB_PASS'];
    $dbname = $_ENV['DB_NAME'];

    $mysqli = new mysqli($host, $user, $pass, $dbname);

    $mysqli->set_charset('utf8mb4');

    if ($mysqli->connect_errno) {
        require_once __DIR__ . '/../Controller/BaseController.php';
        $controller = new BaseController();
        $controller->sendServerError("Ошибка подключения к БД: " . $mysqli->connect_error);
        exit();
    }

    return $mysqli;
}

/**
 * Функция для проверки обязательных переменных .env
 */
function validateEnv(): void
{
    $required = ['DB_HOST', 'DB_USER', 'DB_NAME'];
    foreach ($required as $var) {
        if (empty($_ENV[$var])) {
            throw new Exception("Отсутствует обязательная переменная: $var");
        }
    }
}
