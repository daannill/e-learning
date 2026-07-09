<?php

namespace Core;

class Validator {

    private static array $errors;

    public static function validate(array $data, array $rules, ?array $attributes = null) {
        self::$errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldName = $attributes[$field] ?? ucfirst($field);

            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                $value = trim($data[$field] ?? '');

                if ($rule === 'required') {
                    if ($value === '') {
                        self::$errors[$field] = "$fieldName wajib diisi";
                        break;
                    }
                }

                if ($rule === 'email') {
                    if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        self::$errors[$field] = 'Format email tidak valid';
                        break;
                    }
                }

                if (str_contains($rule, 'min:')) {
                    $min = explode(':', $rule)[1];

                    if (strlen($value) < $min) {
                        self::$errors[$field] = "$fieldName minimal $min karakter";
                        break;
                    }
                }
            }
        }
    }

    public static function check(mixed $condition, string|array $fields, ?string $message = '') {
        if (!$condition) {
            return;
        }

        if (is_array($fields)) {
            foreach ($fields as $field => $msg) {
                if (!isset(self::$errors[$field])) {
                    self::$errors[$field] = $msg;
                }
            }

            return;
        }

        self::$errors[$fields] = $message;
    }

    public static function fails() {
        return !empty(self::$errors);
    }

    public static function errors() {
        return self::$errors;
    }
}