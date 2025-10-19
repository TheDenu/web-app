<?php
require_once './vendor/autoload.php';
require_once './vendor/autoload.php';

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();



class JwtService
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = getenv('JWT_SECRET');
    }

    public function generateToken(array $data, int $expireInSeconds = 3600): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $expireInSeconds;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $data
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken(string $token)
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded->data;
        } catch (Exception $e) {
            return null;
        }
    }
}
