<?php
require_once 'BaseController.php';

class UserController extends BaseController
{
    public function test()
    {
        $this->sendSuccess(['message' => 'API is working']);
    }
}
