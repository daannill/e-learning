<?php

namespace Core;

class Auth {

    public static function info(string $key) {
        return Session::get("auth.$key");
    }

    public static function auth() {
        return Session::has('auth');
    }

    public static function role() {
        return Session::get('auth.role');
    }

    public static function guest() {
        return !self::auth();
    }
}