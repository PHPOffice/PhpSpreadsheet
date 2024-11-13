<?php

declare(strict_types=1);

setlocale(LC_ALL, 'en_US.utf8');

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

        // This code applies only when running release210 with Php8.4.
        // I don't get it at all. I think mitoteam is the victim of circumstance.
        // We need to run PhpUnit 9 because we need to support Php8.0.
        // We are at the highest release of PhpUnit9,
        // but it refers to E_STRICT,
        // which is deprecated in 8.4.
        if (
            str_contains($errstr, 'Constant ')
            && str_contains($errstr, ' already defined')
            && str_contains($filename, 'mitoteam')
        ) {
            return true;
        }

        if (!method_exists(PHPUnit\Framework\TestCase::class, 'setOutputCallback')) {
            throw new Exception("$errstr $filename $lineno");
        }

        throw new Exception("$errstr");
    }

    return false; // continue error handling
}

if (!method_exists(PHPUnit\Framework\TestCase::class, 'setOutputCallback') || PHP_VERSION_ID >= 80400) {
    ini_set('error_reporting', (string) E_ALL);
    set_error_handler('phpunit10ErrorHandler');
}
