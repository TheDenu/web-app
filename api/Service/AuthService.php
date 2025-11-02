<?php

require_once './Controller/BaseController.php';
require_once 'JwtService.php';


class AuthService extends BaseController{
    private $jwtService;

    public function __construct($jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function checkAuth(){
        $headers = getallheaders();

        if(!isset($headers['Authorization'])){
            $this->sendUnauthorized('Missing Authorization header');
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $userData = $this->jwtService->validateToken($token);

        if($userData === null){
            $this->sendUnauthorized('Invalid token');
        }

        return $userData;
    }
}