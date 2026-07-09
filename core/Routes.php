<?php

namespace Core;

class Routes {
    
    public static $routes = [];

    public static function get(string $route, array $action) {
        self::$routes[] = [
            'method' => 'GET',
            'route' => trim($route, '/'),
            'action' => $action,
        ];
    }

    public static function post(string $route, array $action) {
        self::$routes[] = [
            'method' => 'POST',
            'route' => trim($route, '/'),
            'action' => $action,
        ];
    }
}