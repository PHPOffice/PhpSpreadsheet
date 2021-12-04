<?php

$config = [];

if (PHP_VERSION_ID < 80000) {
    // Change of signature in PHP 8.0
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Method .* has invalid return type GdImage\.$~',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Shared/Drawing.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Property .* has unknown class GdImage as its type\.$~',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Worksheet/MemoryDrawing.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Method .* has invalid return type GdImage\.$~',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Worksheet/MemoryDrawing.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Parameter .* of method .* has invalid type GdImage\.$~',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Worksheet/MemoryDrawing.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Class GdImage not found\.$~',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Writer/Xls/Worksheet.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~^Parameter .* of method .* has invalid type GdImage\.$~',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Writer/Xls/Worksheet.php',
        'count' => 1,
    ];
}

return $config;
