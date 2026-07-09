<?php

namespace Core;

class Old {

    public static function set() {
        Session::set('old', Request::post());
    }

    public static function get(string $key) {
        return Session::get("old.$key");
    }

    public static function clear() {
        Session::remove("old");
    }
}