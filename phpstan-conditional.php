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
    // GdImage with Phpstan 1.9.2
    $config['parameters']['ignoreErrors'][] = [
        'message' => '~Class GdImage not found.*$~',
        'path' => __DIR__ . '/tests/PhpSpreadsheetTests/Worksheet/MemoryDrawingTest.php',
        'count' => 3,
    ];
    // Erroneous analysis by Phpstan before PHP8 - 3rd parameter is nullable
    // Fixed for Php7 with Phpstan 1.9.
    //$config['parameters']['ignoreErrors'][] = [
    //    'message' => '#^Parameter \\#3 \\$namespace of method XMLWriter\\:\\:startElementNs\\(\\) expects string, null given\\.$#',
    //    'path' => __DIR__ . '/src/PhpSpreadsheet/Writer/Xlsx/Worksheet.php',
    //    'count' => 8,
    //];
    // Erroneous analysis by Phpstan before PHP8 - mb_strlen does not return false
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Method PhpOffice\\\\PhpSpreadsheet\\\\Shared\\\\StringHelper\\:\\:countCharacters\\(\\) should return int but returns int(<0, max>)?\\|false\\.$#',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Shared/StringHelper.php',
        'count' => 1,
    ];
    // New with Phpstan 1.9.2 for Php7 only
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Parameter \\#2 \\.\\.\\.\\$args of function array_merge expects array, array<int, mixed>\\|false given.$#',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Calculation/LookupRef/Sort.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Parameter \\#1 \\$input of function array_chunk expects array, array<int, float\\|int>\\|false given.$#',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Calculation/MathTrig/MatrixFunctions.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Parameter \\#2 \\$array of function array_map expects array, array<int, float|int>\\|false given.$#',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Calculation/MathTrig/Random.php',
        'count' => 1,
    ];
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Parameter \\#2 \\.\\.\\.\\$args of function array_merge expects array, array<int, mixed>\\|false given.$#',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Calculation/TextData/Text.php',
        'count' => 1,
    ];
} else {
    // Flagged in Php8+ - unsure how to correct code
    $config['parameters']['ignoreErrors'][] = [
        'message' => '#^Binary operation "/" between float and array[|]float[|]int[|]string results in an error.#',
        'path' => __DIR__ . '/src/PhpSpreadsheet/Calculation/MathTrig/Combinations.php',
        'count' => 1,
    ];
}

return $config;
