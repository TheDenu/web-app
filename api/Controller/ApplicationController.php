<?php
require_once 'BaseController.php';
require_once './Middleware/AuthMiddleware.php';
require_once './Model/ApplicationModel.php';

class ApplicationController extends BaseController
{
    protected $mysqli;
    protected $applicationModel;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
        $this->applicationModel = new ApplicationModel($mysqli);
    }

    public function getAll()
    {
        if (!AuthMiddleware::isAdmin()) {
            $this->sendForbidden('Только для админов');
            return;
        }
        $this->sendSuccess($this->applicationModel->getAll());
    }

    public function getMy()
    {
        $user_id = $_SERVER['AUTH_USER_ID'] ?? null;
        if (!$user_id) {
            $this->sendUnauthorized('Не авторизован');
            return;
        }

        $this->sendSuccess($this->applicationModel->getByUser($user_id));
    }

    public function createApplication()
    {
        $user_id = $_SERVER['AUTH_USER_ID'] ?? null;
        if (!$user_id) {
            $this->sendUnauthorized('Не авторизован');
            return;
        }

        error_log("POST: " . print_r($_POST, true));
        error_log("FILES: " . print_r($_FILES, true));

        $data = [
            'place_id' => $_POST['place_id'],
            'description' => $_POST['description'],
            'defect_type_id' => $_POST['defect_type_id'],
            'priority_id' => $_POST['priority_id']
        ];

        error_log("DATA для модели: " . print_r($data, true));

        if (empty($data['place_id']) || empty($data['description'])) {
            $this->sendBadRequest('Отсутствуют обязательные поля: ' . json_encode($data));
            return;
        }

        if ($this->applicationModel->create($user_id, $data, $_FILES['photos'] ?? null)) {
            $this->sendCreate([
                'message' => 'Заявка создана'
            ]);
        } else {
            $this->sendServerError('Ошибка создания заявки');
        }
    }
}
