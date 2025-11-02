<?php
require_once 'BaseController.php';
require_once './Model/ApplicationModel.php';

class ApplicationController extends BaseController
{
    protected $applicationModel;

    public function __construct($mysqli)
    {
        $this->applicationModel = new ApplicationModel($mysqli);
    }

    public function listApplications()
    {
        $applications = $this->applicationModel->getAllApplications();
        $this->sendSuccess($applications);
    }

    public function createApplication()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['floor']) || empty($input['room']) || empty($input['defect_type']) || empty($input['priority']) || empty($input['description']) || empty($input['path'])) {
            $this->sendBadRequest(('Неверные данные'));
            exit();
        }

        if ($this->applicationModel->createApplication($input)) {
            $this->sendCreate(['msg' => 'Заявка успешно создана']);
        } else {
            $this->sendServerError('Ошибка создания заявки');
        }
    }
}
