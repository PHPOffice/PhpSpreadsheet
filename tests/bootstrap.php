<?php

declare(strict_types=1);

setlocale(LC_ALL, 'en_US.utf8');

function phpunit10ErrorHandler(int $errno, string $errstr, string $filename, int $lineno): bool
{
    if ($errno === E_DEPRECATED && suppressPhp85(PHP_VERSION_ID, $filename, $lineno)) {
        return true;
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

function suppressPhp85(int $version, string $filename, int $lineno): bool
{
    if ($version >= 80500) {
        if (str_ends_with($filename, 'jpgraph.php') && $lineno === 1408) {
            return true;
        }
        if (str_ends_with($filename, 'jpgraph_legend.inc.php') && $lineno === 174) {
            return true;
        }
        if (str_ends_with($filename, 'jpgraph_regstat.php') && in_array($lineno, [155, 185, 198, 202], true)) {
            return true;
        }
        if (str_ends_with($filename, 'AttributeTranslator.php') && $lineno === 506) {
            return true;
        }
        if (str_ends_with($filename, 'functions.php') && $lineno === 300) {
            return true;
        }
    }

    return false;
}

if (!method_exists(PHPUnit\Framework\TestCase::class, 'setOutputCallback')) {
    set_error_handler('phpunit10ErrorHandler');
}
