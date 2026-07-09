<?php

namespace Core;

class Flash {

    public static function set(string $key, string|array $value) {
        Session::set("flash.$key", $value);
    }

    public static function get(string $key) {
        $data = Session::get("flash.$key");
        return $data;
    }

    public static function has(string $key) {
        return Session::has("flash.$key");
    }

    public static function clear() {
        Session::remove("flash");
    }
}