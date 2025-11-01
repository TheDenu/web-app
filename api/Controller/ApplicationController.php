<?php
require_once 'BaseController.php';
require_once './Model/UserModel.php';

class ApplicationController extends BaseController {
    protected $applicationModel;

    public function __construct($mysqli)
    {
        $this->applicationModel = new ApplicationModel($mysqli);
    }
}
