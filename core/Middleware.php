<?php

namespace Core;

class Middleware {

    public static function only(array $middlewares) {
        foreach ($middlewares as $middleware) {
            if (method_exists(self::class, $middleware)) {
                self::$middleware();
            }
        }
    }

    public static function guest() {
        if (Auth::auth()) {
            Redirect::to('/');
        }
    }

    public static function auth() {
        if (Auth::guest()) {
            Redirect::to('/login');
        }
    }

    public static function admin() {
        self::auth();

        if (Auth::role() !== 'admin') {
            Abort::error(403);
        }
    }

    public static function teacher() {
        self::auth();

        if (Auth::role() !== 'teacher') {
            Abort::error(403);
        }
    }

    public static function validateCsrf() {
        $token = Request::post('_token');

        if (!$token || !Csrf::validate($token)) {
            Abort::error(403);
        }
    }

}