<?php
class AuthMiddleware
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * Выполняет проверку авторизации и вызывает callback если успешно
     */
    public function handle($next)
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Токен не передан']);
            return;
        }

        $authHeader = $headers['Authorization'];
        if (strpos($authHeader, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['error' => 'Неверный формат токена']);
            return;
        }

        $token = substr($authHeader, 7);

        $stmt = $this->mysqli->prepare("
            SELECT ut.user_id, u.role_id 
            FROM user_tokens ut 
            JOIN users u ON ut.user_id = u.id_user 
            WHERE ut.token = ? AND ut.expires_at > NOW()
        ");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $_SERVER['AUTH_USER_ID'] = $row['user_id'];
            $_SERVER['AUTH_ROLE_ID'] = $row['role_id'];
            $next();
            return;
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Неверный или истекший токен']);
        }
    }

    public static function isAdmin()
    {
        return isset($_SERVER['AUTH_ROLE_ID']) && $_SERVER['AUTH_ROLE_ID'] == 2;
    }
}
