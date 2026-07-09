<?php

namespace Core;

class Session {

    public static function set(string $key, string|array $value) {
        $keys = explode('.', $key);
        $temp = &$_SESSION;

        foreach ($keys as $index => $segment) {
            if ($index === count($keys) - 1) {
                $temp[$segment] = $value;
                return;
            }

            if (!isset($temp[$segment]) || !is_array($temp[$segment])) {
                $temp[$segment] = [];
            }

            $temp = &$temp[$segment];
        }
    }

    public static function get(string $key) {
        $keys = explode('.', $key);
        $temp = $_SESSION;

        foreach ($keys as $segment) {
            if (!isset($temp[$segment])) {
                return null;
            }

            $temp = $temp[$segment];
        }

        return $temp;
    }

    public static function has(string $key) {
        return self::get($key) !== null;
    }

    public static function remove(string $key) {
        $keys = explode('.', $key);
        $lastKey = array_pop($keys);
        $temp = &$_SESSION;

        foreach ($keys as $segment) {
            if (!isset($temp[$segment]) || !is_array($temp[$segment])) {
                return;
            }

            $temp = &$temp[$segment];
        }

        unset($temp[$lastKey]);
    }

    public static function destroy() {
        session_destroy();
    }

    public static function regenerateId() {
        session_regenerate_id(true);
    }
}