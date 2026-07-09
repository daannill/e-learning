<?php

namespace Core;

class Str {

    public static function random(int $length = 6) {
        return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
    }

    public static function userId() {
        return 'USR' . self::random(6);
    }

    public static function enrollmentId() {
        return 'ENR' . self::random(6);
    }

    public static function attemptId() {
        return 'ATT' . self::random(6);
    }
}