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

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $result = $this->applicationModel->getAll($limit, $offset);
        $total = $this->applicationModel->getApplicationsCount();

        $this->sendSuccess([
            'applications' => $result,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit),
                'offset' => $offset
            ]
        ]);
    }

    public function getMy()
    {
        $user_id = $_SERVER['AUTH_USER_ID'] ?? null;
        if (!$user_id) {
            $this->sendUnauthorized('Не авторизован');
            return;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $result = $this->applicationModel->getByUser($user_id, $limit, $offset);

        $total = $this->applicationModel->getUserApplicationsCount($user_id);

        $this->sendSuccess([
            'applications' => $result,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit),
                'offset' => $offset
            ]
        ]);
    }

    public function createApplication(array $input)
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

    public function deleteApplication(array $input)
    {
        $user_id = $_SERVER['AUTH_USER_ID'] ?? null;
        if (!$user_id) {
            $this->sendUnauthorized('Не авторизован');
            return;
        }

        $application_id = (int)($input['id_application'] ?? 0);

        if (!$application_id) {
            $this->sendBadRequest('ID заявки обязателен');
            return;
        }

        if ($this->applicationModel->deleteById($application_id, $user_id)) {
            $this->sendNoContent();
        } else {
            $this->sendForbidden('Нельзя удалить: заявка не на модерации или не найдена');
        }
    }


    public function updateStatus(array $input)
    {
        if (!AuthMiddleware::isAdmin()) {
            $this->sendForbidden('Только для администраторов');
            return;
        }

        $application_id = (int)($input['application_id'] ?? 0);
        $status_id = (int)($input['status_id'] ?? 0);

        if (!$application_id || !$status_id) {
            $this->sendBadRequest('ID заявки и статус обязательны');
            return;
        }

        if ($this->applicationModel->updateStatus($application_id, $status_id)) {
            $this->sendSuccess(['message' => 'Статус заявки обновлён']);
        } else {
            $this->sendServerError('Ошибка обновления статуса');
        }
    }
}
