<?php

require_once './Controller/BaseController.php';
require_once './Model/UserModel.php';
require_once 'JwtService.php';


class AuthService extends BaseController{
    private $jwtService;
    private $userModel;

    public function __construct($jwtService, $userModel)
    {
        $this->jwtService = $jwtService;
        $this->userModel = $userModel;
    }

    public function getBearerToken(): ?string{
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            $this->sendUnauthorized('Missing Authorization header');
            exit;
        }
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        return $token;
    }
    public function getUserIdFromToken(): ?int{
        $token = $this->getBearerToken();
        if(!$token){
            return null;
        }

        $payloadObject = $this->jwtService->validateToken($token);
        $payload = json_decode(json_encode($payloadObject), true);
        return $payload['user_id'] ?? null;
    }

    public function checkAuth(){
        $token = $this->getBearerToken();
        $userData = $this->jwtService->validateToken($token);

        if($userData === null){
            $this->sendUnauthorized('Invalid token');
        }

        return $userData;
    }

    public function isAdmin(): bool{
        $userId = $this->getUserIdFromToken();
        if(!$userId){
            return false;
        }

        $role = $this->userModel->getUserRoleById($userId);
        return $role === 'admin';
    }

    public function checkAdmin(){
        $this->checkAuth();
        if(!$this->isAdmin()){
            $this->sendForbidden('Недостаточно доступа');
        }
    }
}