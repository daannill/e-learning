<?php

session_start();

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'helpers.php';

use Core\App;

$app = new App();