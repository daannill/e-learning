<?php

use Core\Csrf;
use Core\Auth;
use Core\Old;
use Core\Flash;

function csrfToken() {
    return Csrf::token();
}

function csrf() {
    return '
        <input
            type="hidden"
            name="_token"
            value="' . Csrf::token() . '"
        >
    ';
}

function authInfo(string $key) {
    return Auth::info($key);
}

function auth() {
    return Auth::auth();
}

function guest() {
    return Auth::guest();
}

function teacher() {
    return Auth::role() == 'teacher'; 
}

function old(string $key) {
    return Old::get($key);
}

function hasFlash(string $key) {
    return Flash::has($key);
}

function flash(string $key) {
    return Flash::get($key);
}

function invalid(string $key) {
    return Flash::has("errors.$key") ? 'error' : '';
}

function error(string $key) {

    if(!Flash::has("errors.$key")){
        return '';
    }

    return '
        <small class="form-error">
        '.
            Flash::get("errors.$key")
        .'
        </small>
    ';
}

function successAlert() {
    
    if(!Flash::has('success')) {
        return '';
    }

    return '
        <div class="alert alert-success alert-floating">
            ' . Flash::get('success') . '
        </div>
    ';
}

function asset(string $path) {
    return BASEURL . '/assets/' . $path;
}

function css(array $files = []){

    if(empty($files)){
        return;
    }

    foreach($files as $file){

        echo '
            <link
                rel="stylesheet"
                href="' . asset('css/' . $file . '.css') . '">
        ';
    }
}

function js(array $files = []){

    if(empty($files)){
        return;
    }

    foreach($files as $file){

        echo '
            <script
                src="' . asset('js/' . $file . '.js') . '">
            </script>
        ';
    }
}

function active(string $url) {

    $current = trim($_GET['url'] ?? '', '/');
    $url = trim($url, '/');

    if($url === '') {
        return $current === '' ? 'active' : '';
    }

    return str_starts_with($current, $url)
        ? 'active'
        : '';
}

function timeAgo(string $datetime) {
    
    $time = strtotime($datetime);
    $diff = time() - $time;

    $units = [
        31536000 => 'year',
        2592000  => 'month',
        86400    => 'day',
        3600     => 'hour',
        60       => 'minute',
        1        => 'second'
    ];

    foreach ($units as $seconds => $label) {

        if ($diff >= $seconds) {

            $value = floor($diff / $seconds);

            return $value . ' ' . $label . ($value > 1 ? 's' : '') . ' ago';
        }
    }

    return 'just now';
}

function numberShort(int $number): string {

    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    }

    if ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }

    return (string) $number;
}