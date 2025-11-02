<?php
require_once 'BaseController.php';
require_once './Model/UserModel.php';

class UserController extends BaseController
{
    protected $userModel;

    public function __construct($mysqli, $jwtService)
    {
        $this->userModel = new UserModel($mysqli, $jwtService);
    }

    public function registration()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['login']) || empty($input['password']) || empty($input['fio'])) {
            $this->sendBadRequest('Неверные данные');
            exit();
        }

        if ($this->userModel->existsByLogin($input['login'])) {
            $this->sendBadRequest('Пользователь с таким логином уже существует');
            exit();
        }

        $passwordHash = password_hash($input['password'], PASSWORD_BCRYPT);
        $input['password'] = $passwordHash;

        if ($this->userModel->createUser($input)) {
            $this->sendCreate(['msg' => 'Пользователь успешно зарегистрирован']);
        } else {
            $this->sendServerError('Ошибка создания пользователя');
        }
    }

    public function login()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['login']) || empty($input['password'])) {
            $this->sendBadRequest('Неверные данные');
            exit();
        }

        $user = $this->userModel->existsUser($input);

        if ($user === null) {
            $this->sendBadRequest('Неверный логин или пароль');
            exit();
        }

        $token = $this->userModel->createToken($user);

        echo json_encode(['token' => $token]);
    }
}
