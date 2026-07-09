<?php

namespace Core;

class Response {

    public static function json(mixed $data) {
        if (headers_sent()) {
            Abort::error(500);
        }

        $json = json_encode($data);

        if ($json === false) {
            Abort::error(500);
        }

        header('Content-Type: application/json');

        echo $json;

        exit;
    }
}