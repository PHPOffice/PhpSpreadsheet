#!/usr/bin/env php
<?php

function findPolyfill(string $directory): int
{
    // See issue #4215 - code which should have erred in unit test
    // succeeded because a dev package required polyfills.
    $retCode = 0;
    $polyfill80 = 'FILTER_VALIDATE_BOOL\b'
        . '|fdiv[(]'
        . '|preg_last_error_msg[(]'
        . '|str_contains[(]'
        . '|str_starts_with[(]'
        . '|str_ends_with[(]'
        . '|get_debug_type[(]'
        . '|get_resource_id[(]';
    $polyfill81 = 'MYSQLI_REFRESH_REPLICA\b'
        . '|fdiv[(]'
        . '|array_is_list[(]'
        . '|enum_exists[(]';
    $polyfill = '/\b(?:'
        . $polyfill80
        . '|'
        . $polyfill81
        . ')/';

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::UNIX_PATHS)
    );

    foreach ($it as $file) {
        if ($file->getExtension() === 'php') {
            $fullname = $it->getPath() . '/' . $it->getBaseName();
            $contents = file_get_contents($fullname);
            if ($contents === false) {
                echo "failed to read $fullname\n";
                ++$retCode;
            } elseif (preg_match_all($polyfill, $contents, $matches)) {
                var_dump($fullname, $matches);
                ++$retCode;
            }
        }
    }

    return $retCode;
}

// Don't care if tests use polyfill
$errors = findPolyfill(__DIR__ . '/../src') + findPolyfill(__DIR__ . '/../samples');
if ($errors !== 0) {
    echo "Found $errors files that might require polyfills\n";
    exit(1);
}
echo "No polyfills needed\n";
