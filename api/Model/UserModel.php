<?php

class UserModel
{
    protected $mysqli;
    protected $jwtService;

    public function __construct($mysqli, $jwtService)
    {
        $this->mysqli = $mysqli;
        $this->jwtService = $jwtService;
    }

    public function getByFio(string $fio)
    {
        $stmt = $this->mysqli->prepare("SELECT id_fio FROM names WHERE fio = ? LIMIT 1");
        $stmt->bind_param("s", $fio);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        return $row ? (int)$row[0] : null;
    }

    public function existsByLogin(string $login): bool
    {
        $stmt = $this->mysqli->prepare("SELECT id_user FROM users WHERE login = ? LIMIT 1");
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }


    public function createUser(int $idFio, string $login, string $hashedPassword, int $roleId = 1): bool
    {
        $stmt = $this->mysqli->prepare("
            INSERT INTO users
            (login, password, fio_id, role_id) VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssii", $login, $hashedPassword, $idFio, $roleId);

        return $stmt->execute();
    }

    public function existsUser(array $data)
    {
        $stmt = $this->mysqli->prepare("
            SELECT users.id_user, users.password, roles.role_name 
            FROM users 
            JOIN roles ON users.role_id = roles.id_role 
            WHERE users.login = ?
        ");
        $stmt->bind_param('s', $data['login']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($data['password'], $row['password'])) {
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

        $stmt = $this->mysqli->prepare("SELECT token FROM user_tokens WHERE user_id = ? LIMIT 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $token = $row['token'];
            $payload = $this->jwtService->validateToken($token);
            if ($payload !== null) {
                return $token;
            }

            $this->mysqli->prepare("DELETE FROM user_tokens WHERE user_id = ?")->execute([$userId]);
        }

        $token = $this->jwtService->generateToken([
            'userId' => $userId,
            'role' => $user['role']
        ]);

        $expiresAt = date('Y-m-d H:i:s', time() + 3600 * 24);
        $stmt = $this->mysqli->prepare("
            INSERT INTO user_tokens (user_id, token, expires_at) 
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $userId, $token, $expiresAt);
        $stmt->execute();

        return $token;
    }

    public function deleteToken(string $token): bool
    {
        $stmt = $this->mysqli->prepare("DELETE FROM user_tokens WHERE token = ?");
        $stmt->bind_param("s", $token);

        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        }
        return false;
    }
}
