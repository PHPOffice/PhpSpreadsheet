<?php
spl_autoload_register(function ($class) {
    include 'phar://PHPExcel/' . str_replace('_', '/', $class) . '.php';
});

try {
    Phar::mapPhar();
    include 'phar://PHPExcel/PHPExcel.php';
} catch (PharException $e) {
    error_log($e->getMessage());
    exit(1);
}

__HALT_COMPILER();