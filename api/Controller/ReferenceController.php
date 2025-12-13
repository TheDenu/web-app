<?php
require_once './Model/ReferenceModel.php';
require_once 'BaseController.php';


class ReferenceController extends BaseController
{
    private $model;

    public function __construct($mysqli)
    {
        $this->model = new ReferenceModel($mysqli);
    }

    public function getStatuses()
    {
        $this->sendSuccess($this->model->getStatuses());
    }

    public function getPlaces()
    {
        $this->sendSuccess($this->model->getPlaces());
    }

    public function getDefectTypes()
    {
        $this->sendSuccess($this->model->getDefectTypes());
    }

    public function getPriorities()
    {
        $this->sendSuccess($this->model->getPriorities());
    }
}
