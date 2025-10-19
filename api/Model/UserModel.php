<?php
require_once './Service/DBConnect.php';

class UserModel
{
    protected $mysqli;
    protected $jwtService;

    public function __construct($mysqli, $jwtService)
    {
        $this->mysqli = $mysqli;
        $this->jwtService = $jwtService;
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
        $password = $this->mysqli->real_escape_string($data['password']);

        $query = "SELECT id_user FROM users WHERE login = '$login' and password = '$password' limit 1";
        $result = $this->mysqli->query($query);
        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return (int)$row['id_user'];
        }
        return null;
    }

    public function createToken(int $userId)
    {
        $token = $this->jwtService->generateToken(['userId' => $userId]);
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        $stmt = $this->mysqli->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $token, $expiresAt);
        $stmt->execute();
        $stmt->close();
        return $token;
    }
}
