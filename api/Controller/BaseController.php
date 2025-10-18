<?php
class BaseController
{
    protected function sendResponse($data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function sendSuccess($data)
    {
        $this->sendResponse($data, 200);
    }

    protected function sendCreate($data)
    {
        $this->sendResponse($data, 201);
    }

    protected function sendNoContent($data)
    {
        http_response_code(204);
        exit();
    }

    protected function sendBadRequest($msg = "Bad Request")
    {
        $this->sendResponse(['error' => $msg], 400);
    }

    protected function sendNotFound($msg = "Not Found")
    {
        $this->sendResponse(['error' => $msg], 404);
    }

    protected function sendUnauthorized($msg = "Unauthorized")
    {
        $this->sendResponse(['error' => $msg], 401);
    }

    protected function sendForbidden($msg = "Forbidden")
    {
        $this->sendResponse(['error' => $msg], 403);
    }

    public function sendServerError($msg = "Internal Server Error")
    {
        $this->sendResponse(['error' => $msg], 500);
    }
}
