<?php

setlocale(LC_ALL, 'en_US.utf8');

// PHP 5.3 Compat
//date_default_timezone_set('Europe/London');

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

        throw new \Exception("$errstr $filename $lineno");
    }

    return false; // continue error handling
}

if (!method_exists(\PHPUnit\Framework\TestCase::class, 'setOutputCallback')) {
    ini_set('error_reporting', E_ALL);
    set_error_handler('phpunit10ErrorHandler');
}
