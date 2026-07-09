<?php

namespace Core;

class Csrf {

    public static function token() {
        if (!Session::has('_token')) {
            Session::set('_token', bin2hex(random_bytes(32)));
        }

        return Session::get('_token');
    }

    public static function validate(string $token) {
        return Session::has('_token') && hash_equals(Session::get('_token'), $token);
    }
}