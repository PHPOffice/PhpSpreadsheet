<?php

declare(strict_types=1);

setlocale(LC_ALL, 'en_US.utf8');
ini_set('error_reporting', (string) E_ALL);

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

function phpunit10ErrorHandler(int $errno, string $errstr, string $filename, int $lineno): bool
{
    $x = error_reporting() & $errno;
    if (
        in_array(
            $errno,
            [
                E_DEPRECATED,
                E_WARNING,
                E_NOTICE,
                E_USER_DEPRECATED,
                E_USER_NOTICE,
                E_USER_WARNING,
            ],
            true
        )
    ) {
        if (0 === $x) {
            return true; // message suppressed - stop error handling
        }

        throw new Exception("$errstr $filename $lineno");
    }

    return false; // continue error handling
}

set_error_handler('phpunit10ErrorHandler');
