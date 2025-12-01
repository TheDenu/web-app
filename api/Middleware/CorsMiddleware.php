<?php

class CorsMiddleware
{
    public function handle()
    {
        // Разрешить доступ с любого источника (для разработки)
        header("Access-Control-Allow-Origin: *");

        // Разрешить методы
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

        // Разрешить заголовки
        header("Access-Control-Allow-Headers: Authorization, Content-Type, Accept");

        // При preflight-запросе OPTIONS просто завершаем
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}
