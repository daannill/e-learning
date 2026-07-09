<?php

namespace Core;

class Abort {
    
    public static function error(int $code = 404) {
        $allowedCodes = [403, 404, 500];

        if (!in_array($code, $allowedCodes, true)) {
            $code = 500;
        }

        http_response_code($code);

        $view = "app/views/errors/$code.php";

        if (!file_exists($view)) {
            $view = 'app/views/errors/500.php';
        }

        require $view;

        exit;
    }
}