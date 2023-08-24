<?php
class Router
{
    private $routes = [];

    public function addRoute($method, $path, $controllerMethod)
    {
        $this->routes[] = ['method' => $method, 'path' => $path, 'controllerMethod' => $controllerMethod];
    }

    public function handleRequest()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            $pattern = $this->patternToRegex($route['path']);

            if ($route['method'] === $requestMethod && preg_match($pattern, $requestPath, $matches)) {
                $controllerMethod = explode('@', $route['controllerMethod']);
                $controllerName = $controllerMethod[0];
                $methodName = $controllerMethod[1];

                $params = $this->extractParams($pattern, $matches);

                if ($requestMethod !== 'GET' && in_array($requestMethod, ['POST', 'PUT', 'PATCH'])) {
                    $requestBody = file_get_contents('php://input');
                    $data = json_decode($requestBody, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $params['data'] = $data;
                    }
                }
                if($requestMethod === 'GET' && count($matches) > 1){
                    $params['id'] = $matches["id"];
                }

                require_once 'controllers/' . $controllerName . '.php';
                $controller = new $controllerName();
                $controller->$methodName(...$params);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not Found']);
    }

    private function extractParams($pattern, $matches)
    {
        preg_match_all('/{([^}]+)}/', $pattern, $paramNames);
        return array_intersect_key($matches, array_flip($paramNames[1]));
    }

    private function patternToRegex($pattern)
    {
        return '#^' . str_replace(['/', '{', '}'], ['\/', '(?P<', '>[^\/]+)'], $pattern) . '$#';
    }
}
