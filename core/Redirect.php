<?php

namespace Core;

class Redirect {

    public static function to(string $path) {
        header('Location: ' . BASEURL . $path);

        exit;
    }
    
}