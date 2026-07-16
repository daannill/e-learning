<?php

namespace Core;

class Request {

    public static function post(string|null $key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }

        return $_POST[$key] ?? $default;
    }

    public static function get(?string $key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }

    public static function file(?string $key = null, $default = null) {
        if ($key === null) {
            return $_FILES;
        }

        return $_FILES[$key] ?? $default;
    }

    public static function hasFile(string $key): bool {
        if (!isset($_FILES[$key])) {
            return false;
        }

        return $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public static function hasPost(string $key) {
        return isset($_POST[$key]);
    }

    public static function hasGet(string $key): bool {
        return isset($_GET[$key]);
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