<?php

namespace Core;

class File {

    public static function upload(
        array $file,
        string $directory,
        ?string $filename = null
    ): string|false {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        $filename ??= bin2hex(random_bytes(16));

        $path = sprintf(
            '%s/%s.%s',
            rtrim(str_replace('\\', '/', $directory), '/'),
            $filename,
            $extension
        );

        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return false;
        }

        return "$filename.$extension";
    }

    public static function delete(string $path): bool {
        if (!is_file($path)) {
            return false;
        }

        return unlink($path);
    }

    public static function exists(string $path): bool {
        return is_file($path);
    }

    public static function rename(string $from, string $to) {
        if (!file_exists($from)) {
            return false;
        }

        $directory = dirname($to);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        return rename($from, $to);
    }
}