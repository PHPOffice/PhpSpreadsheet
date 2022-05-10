<?php

$config = [];

if (PHP_VERSION_ID < 80000) {
    // GdImage not available before PHP8
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
    // Erroneous analysis by Phpstan before PHP8 - 3rd parameter is nullable
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Parameter \\#3 \\$namespace of method XMLWriter\\:\\:startElementNs\\(\\) expects string, null given\\.$#',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Writer/Xlsx/Worksheet.php',
        'count' => 8,
    ];
    // Erroneous analysis by Phpstan before PHP8 - mb_strlen does not return false
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Method PhpOffice\\\\PhpSpreadsheet\\\\Shared\\\\StringHelper\\:\\:countCharacters\\(\\) should return int but returns int(<0, max>)?\\|false\\.$#',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Shared/StringHelper.php',
        'count' => 1,
    ];
} else {
    // Flagged in Php8+ - unsure how to correct code
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Binary operation "/" between float and array[|]float[|]int[|]string results in an error.#',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Calculation/MathTrig/Combinations.php',
        'count' => 2,
    ];
}

return $config;
