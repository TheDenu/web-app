<?php
require_once './Service/DBConnect.php';
require_once './Service/JwtService.php';

class UserModel
{
    protected $mysqli;
    protected $jwtService;

    public function __construct($mysqli, $jwtService)
    {
        $this->mysqli = $mysqli;
        $this->jwtService = $jwtService;
    }

    public function getUserRoleById(int $userId): ?string
    {
        $stmt = $this->mysqli->prepare("
            SELECT roles.role_name 
            FROM users 
            JOIN roles on roles.id_role = users.role_id 
            WHERE id = ?
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['role_name'];
        }
        return null;
    }

    public function existsByLogin(string $login): bool
    {
        $loginEscaped = $this->mysqli->real_escape_string($login);

        $query = "SELECT id_user FROM users WHERE login = '$loginEscaped' limit 1";
        $result = $this->mysqli->query($query);

        return $result && $result->num_rows > 0;
    }

    public function createUser(array $data): bool
    {
        $login = $this->mysqli->real_escape_string($data['login']);
        $password = $this->mysqli->real_escape_string($data['password']);
        $fio = $this->mysqli->real_escape_string($data['fio']);

        $query = "INSERT INTO users (login, password, fio) VALUES ('$login', '$password', '$fio')";
        return $this->mysqli->query($query);
    }

    public function existsUser(array $data)
    {
        $login = $this->mysqli->real_escape_string($data['login']);
        $inputPassword = $data['password'];

        $query = "SELECT users.id_user, users.password, roles.role_name FROM users JOIN roles on users.role_id = roles.id_role WHERE users.login = '$login' limit 1";
        $result = $this->mysqli->query($query);
        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
            if (password_verify($inputPassword, $hashedPassword)) {
                return [
                    'id_user' => (int)$row['id_user'],
                    'role' => $row['role_name'],
                ];
            }
        }
        return null;
    }

    public function createToken($user)
    {
        $userId = $user['id_user'];
        $userRole = $user['role'];

        $stmt = $this->mysqli->prepare("SELECT token FROM user_tokens WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $token = $row['token'];

            $data = $this->jwtService->validateToken($token);
            if ($data !== null) {
                return $token;
            } else {
                $this->mysqli->query("DELETE FROM user_tokens WHERE user_id = $userId");
            }
        }

        $token = $this->jwtService->generateToken(['userId' => $userId, 'role' => $userRole]);
        date_default_timezone_set('Europe/Moscow');
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);
        $createdAt = date('Y-m-d H:i:s', time());

        $stmt = $this->mysqli->prepare("INSERT INTO user_tokens (user_id, token, created_at, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $token, $createdAt, $expiresAt);
        $stmt->execute();
        $stmt->close();

        return $token;
    }
}
