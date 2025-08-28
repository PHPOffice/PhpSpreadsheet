<?php

declare(strict_types=1);

setlocale(LC_ALL, 'en_US.utf8');

function phpunit10ErrorHandler(int $errno, string $errstr, string $filename, int $lineno): bool
{
    if (strIncrement85(PHP_VERSION_ID, $errno, $errstr, $filename)) {
        return true; // message suppressed - stop error handling
    }
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

function strIncrement85(int $version, int $errno, string $errstr, string $filename): bool
{
    if ($version < 80500 || $errno !== E_DEPRECATED) {
        return false;
    }
    if (preg_match('/canonical/', $errstr) === 1 && preg_match('/mitoteam/', $filename) === 1) {
        return true;
    }

    return false;
}

if (!method_exists(PHPUnit\Framework\TestCase::class, 'setOutputCallback')) {
    set_error_handler('phpunit10ErrorHandler');
}
