<?php

namespace Core;

class App {

    public function __construct() {
        require 'routes/web.php';

        $url = trim($_GET['url'] ?? '', '/');
        $requestMethod = Request::method();

        foreach (Routes::$routes as $routeData) {
            if ($requestMethod !== $routeData['method']) {
                continue;
            }

            $route = $routeData['route'];

            if (strpos($route, '{') === false) {
                if ($url === $route) {
                    $this->runAction($routeData['action']);
                    return;
                }

                continue;
            }

            preg_match_all(
                '/\{([a-zA-Z0-9_]+)\}/',
                $route,
                $paramNames
            );

            $pattern = preg_replace(
                '/\{([a-zA-Z0-9_]+)\}/',
                '([^/]+)',
                $route
            );

            $pattern = "#^{$pattern}$#";

            if (!preg_match($pattern, $url, $matches)) {
                continue;
            }

            array_shift($matches);

            $params = array_combine($paramNames[1], $matches) ?: [];

            $this->runAction($routeData['action'], $params);
            return;
        }

        Abort::error(404);
    }

    private function runAction(array $action, $params = []) {
        [$controllerName, $method] = $action;

        if (!class_exists($controllerName)) {
            Abort::error(500);
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $method)) {
            Abort::error(500);
        }

        $controller->runMiddleware($method);

        call_user_func([$controller, $method], $params);
    }
}