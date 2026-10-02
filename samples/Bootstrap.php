<?php

// Bootstrap for PhpSpreadsheet samples.

if (!defined('K_PATH_FONTS')) {
    $path1 = __DIR__ . '/..'
        . '/vendor/tecnickcom/tc-lib-pdf-font';
    $realpath1 = realpath($path1);
    if ($realpath1 !== false) {
        $path = __DIR__ . '/..'
            . '/tclibpdffonts/fonts/';
        $path = realpath($path);
        if ($path !== false) {
            define(
                'K_PATH_FONTS',
                $path
            );
        }
    }
}

// This is a pain, but we have to try to find the composer autoloader

$paths = [
    __DIR__ . '/../vendor/autoload.php', // In case PhpSpreadsheet is cloned directly
    __DIR__ . '/../../../autoload.php', // In case PhpSpreadsheet is a composer dependency.
];

foreach ($paths as $path) {
    if (file_exists($path)) {
        require_once $path;

        return;
    }
}

throw new Exception('Composer autoloader could not be found. Install dependencies with `composer install` and try again.');
