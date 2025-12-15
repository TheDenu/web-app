<?php
class Router
{
    private $routes = [];
    private $mysqli;
    private $controllers = [];
    private $auth;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
        $this->initControllers();
        $this->initAuth();
        $this->initRoutes();
    }

    private function initAuth()
    {
        $this->auth = new AuthMiddleware($this->mysqli);
    }

    private function initControllers()
    {
        require_once 'Service/JwtService.php';
        require_once 'Model/UserModel.php';
        require_once 'Controller/UserController.php';
        require_once 'Controller/ApplicationController.php';
        require_once 'Controller/ReferenceController.php';
        require_once 'Middleware/AuthMiddleware.php';
        require_once 'Service/Validator.php';

        $jwtService = new JwtService();

        $this->controllers = [
            'user' => new UserController($this->mysqli, $jwtService),
            'application' => new ApplicationController($this->mysqli),
            'reference' => new ReferenceController($this->mysqli)
        ];
    }

    private function initRoutes()
    {
        $this->routes['GET']['user/me'] = fn($input) => $this->auth->handle(fn() => $this->controllers['user']->getMe());
        $this->routes['POST']['registration'] = function ($input) {
            $validator = new Validator();
            $rules = [
                'login' => ['required', 'string', 'min:3', 'max:32', 'alpha_num'],
                'password' => ['required', 'string', 'min:8'],
                'fio' => ['required', 'string', 'min:5', 'max:100']
            ];

            if (!$validator->validate($input, $rules)) {
                http_response_code(400);
                echo json_encode($validator->getErrors());
                return;
            }
            $this->controllers['user']->registration($input);
        };
        $this->routes['POST']['login'] = function ($input) {
            $validator = new Validator();
            $rules = [
                'login' => ['required', 'string'],
                'password' => ['required', 'string']
            ];

            if (!$validator->validate($input, $rules)) {
                http_response_code(400);
                echo json_encode($validator->getErrors());
                return;
            }
            $this->controllers['user']->login($input);
        };

        $this->routes['POST']['logout'] = fn($input) => $this->auth->handle(fn() => $this->controllers['user']->logout());

        $this->routes['POST']['application/create'] = function ($input) {
            $validator = new Validator();
            $rules = [
                'place_id' => ['required', 'int', 'min:1'],
                'description' => ['required', 'string', 'min:5', 'max:1000'],
                'defect_type_id' => ['required', 'int', 'min:1', 'max:3'],
                'priority_id' => ['required', 'int', 'min:1', 'max:3']
            ];

            if (!$validator->validate($input, $rules)) {
                http_response_code(400);
                echo json_encode($validator->getErrors());
                return;
            }
            $this->auth->handle(fn() => $this->controllers['application']->createApplication($input));
        };
        $this->routes['GET']['application/list'] = fn($input) => $this->auth->handle(function () {
            if (AuthMiddleware::isAdmin()) {
                $this->controllers['application']->getAll();
            } else {
                http_response_code(403);
                echo json_encode(['error' => 'Только для администраторов']);
            }
        });
        $this->routes['GET']['application/my-list'] = fn($input) => $this->auth->handle(fn() => $this->controllers['application']->getMy());
        $this->routes['DELETE']['application/delete'] = function ($input) {
            $validator = new Validator();
            $rules = ['id_application' => ['required', 'int', 'min:1']];

            if (!$validator->validate($input, $rules)) {
                http_response_code(400);
                echo json_encode($validator->getErrors());
                return;
            }
            $this->auth->handle(fn() => $this->controllers['application']->deleteApplication($input));
        };
        $this->routes['PUT']['application/update-status'] = function ($input) {
            $validator = new Validator();
            $rules = [
                'application_id' => ['required', 'int', 'min:1'],
                'status_id' => ['required', 'int', 'min:1']
            ];

            if (!$validator->validate($input, $rules)) {
                http_response_code(400);
                echo json_encode($validator->getErrors());
                return;
            }
            $this->auth->handle(fn() => $this->controllers['application']->updateStatus($input));
        };

        $this->routes['GET']['reference/statuses'] = fn($input) => $this->auth->handle(fn() => $this->controllers['reference']->getStatuses());
        $this->routes['GET']['reference/places'] = fn($input) => $this->auth->handle(fn() => $this->controllers['reference']->getPlaces());
        $this->routes['GET']['reference/defect-types'] = fn($input) => $this->auth->handle(fn() => $this->controllers['reference']->getDefectTypes());
        $this->routes['GET']['reference/priorities'] = fn($input) => $this->auth->handle(fn() => $this->controllers['reference']->getPriorities());
    }


    private function getInputData(): array
    {
        $method = $_SERVER['REQUEST_METHOD'];

        return match ($method) {
            'GET', 'DELETE' => $_GET,
            'POST', 'PUT' => array_merge($_POST, json_decode(file_get_contents('php://input'), true) ?: []),
            default => []
        };
    }

    public function dispatch($method, $uri)
    {
        $path = trim(parse_url($uri, PHP_URL_PATH), '/');
        if (!str_starts_with($path, 'api/')) {
            http_response_code(404);
            echo json_encode(['error' => 'API endpoint required']);
            return;
        }
        $path = substr($path, 4);

        $input = $this->getInputData();

        if (isset($this->routes[$method][$path])) {
            $this->routes[$method][$path]($input);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Маршрут не найден']);
        }
    }
}
