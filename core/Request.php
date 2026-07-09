<?php

namespace Core;

class Request {

    public static function post(string|null $key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }

        return $_POST[$key] ?? $default;
    }

    public static function hasPost(string $key) {
        return isset($_POST[$key]);
    }

    public static function method() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function isPost() {
        return self::method() === 'POST';
    }

    public static function isGet() {
        return self::method() === 'GET';
    }   
}