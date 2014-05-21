<?php

// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'composer.lock')) {
    die("Dependencies must be installed using composer:\n\ncomposer.phar install --dev\n");
}

// Include the composer autoloader
$loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
