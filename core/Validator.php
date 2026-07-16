<?php

namespace Core;

class Validator {

    private static array $errors = [];

    public static function validate(array $data, array $rules, ?array $attributes = null) {
        foreach ($rules as $field => $ruleString) {
            $fieldName = $attributes[$field] ?? ucfirst($field);

            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                $value = trim($data[$field] ?? '');

                if ($rule === 'required') {
                    if ($value === '') {
                        self::setError($field, "$fieldName wajib diisi");
                        break;
                    }
                }

                if ($rule === 'email') {
                    if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        self::setError($field, 'Format email tidak valid');
                        break;
                    }
                }

                if (str_contains($rule, 'min:')) {
                    $min = explode(':', $rule)[1];

                    if (strlen($value) < $min) {
                        self::setError($field, "$fieldName minimal $min karakter");
                        break;
                    }
                }
            }
        }
    }

    public static function validateFile(
        array $files,
        array $rules,
        ?array $attributes = null
    ) {
        foreach ($rules as $field => $ruleString) {
            $fieldName = $attributes[$field] ?? ucfirst($field);
            $ruleList = explode('|', $ruleString);
            $file = $files[$field] ?? null;

            foreach ($ruleList as $rule) {
                if ($rule === 'required') {
                    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
                        self::setError($field, "$fieldName wajib diisi");
                        break;
                    }

                    continue;
                }

                if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
                    continue;
                }

                if ($rule === 'image') {
                    if (!str_starts_with($file['type'], 'image/')) {
                        self::setError($field, "$fieldName harus berupa gambar");
                        break;
                    }

                    continue;
                }

                if (str_starts_with($rule, 'mimes:')) {
                    $allowed = explode(':', $rule, 2)[1];
                    $allowed = explode(',', $allowed);

                    $extension = strtolower(
                        pathinfo($file['name'], PATHINFO_EXTENSION)
                    );

                    if (!in_array($extension, $allowed, true)) {
                        self::setError($field, "$fieldName memiliki format yang tidak didukung");
                        break;
                    }

                    continue;
                }

                if (str_starts_with($rule, 'max:')) {
                    $max = (int) explode(':', $rule, 2)[1];

                    if ($file['size'] > ($max * 1024 * 1024)) {
                        self::setError($field, "$fieldName maksimal {$max}MB");
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
                self::setError($field, $msg);
            }

            return;
        }

        self::setError($fields, $message);
    }

    public static function fails() {
        return !empty(self::$errors);
    }

    public static function errors() {
        return self::$errors;
    }

    private static function setError(string $key, string $message) {
        $keys = explode('.', $key);
        $temp = &self::$errors;

        foreach ($keys as $index => $segment) {
            if ($index === count($keys) - 1) {
                $temp[$segment] = $message;
                return;
            }

            if (!isset($temp[$segment]) || !is_array($temp[$segment])) {
                $temp[$segment] = [];
            }

            $temp = &$temp[$segment];
        }
    }
}