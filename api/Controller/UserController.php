<?php
require_once './Model/UserModel.php';
require_once 'BaseController.php';

class UserController extends BaseController
{
    protected $userModel;

    public function __construct($mysqli, $jwtService)
    {
        $this->userModel = new UserModel($mysqli, $jwtService);
    }

    public function getMe()
    {
        $userId = $_SERVER['AUTH_USER_ID'];

        $user = $this->userModel->getById($userId);

        if ($user === null) {
            $this->sendBadRequest('Пользователь не найден');
            return;
        }

        $user['role'] = $user['role_id'] == 2 ? 'admin' : 'user';
        $this->sendSuccess($user);
    }
    public function registration(array $input)
    {
        $idFio = $this->userModel->getByFio($input['fio']);

        if ($idFio === null) {
            $this->sendBadRequest('Пользователь с таким ФИО не найден');
            return;
        }

        if ($this->userModel->fioExists($idFio)) {
            $this->sendBadRequest('Аккаунт с таким ФИО уже зарегистрирован');
            return;
        }

        if ($this->userModel->existsByLogin($input['login'])) {
            $this->sendBadRequest('Пользователь с таким логином уже существует');
            return;
        }

        $passwordHash = password_hash($input['password'], PASSWORD_BCRYPT);

        if ($this->userModel->createUser($idFio, $input['login'], $passwordHash, 1)) {
            $this->sendCreate(['msg' => 'Пользователь успешно зарегистрирован']);
        } else {
            $this->sendServerError('Ошибка создания пользователя');
        }
    }

    public function login(array $input)
    {
        $user = $this->userModel->existsUser($input);

        if ($user === null) {
            $this->sendUnauthorized('Неверный логин или пароль');
            return;
        }

        $token = $this->userModel->createToken($user);

        $this->sendCreate([
            'token' => $token,
            'user' => [
                'id' => $user['id_user'],
                'role' => $user['role']
            ]
        ]);
    }

    public function logout()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'];

        $token = substr($authHeader, 7);

        if ($this->userModel->deleteToken($token)) {
            $this->sendSuccess(['msg' => 'Пользователь успешно вышел из системы']);
        } else {
            $this->sendServerError('Ошибка при выходе из системы');
        }
    }
}
