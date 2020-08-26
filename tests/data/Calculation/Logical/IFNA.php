<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

return [
    'A numeric value should return that value' => [
        34.5,
        34.5, 'not found',
    ],
    'A boolean value should return that value' => [
        true,
        true, 'not found',
    ],
    'A null value should return that value' => [
        null,
        null, 'not found',
    ],
    'A string value should return that value' => [
        'Hello World',
        'Hello World', 'not found',
    ],
    'A string value containing an error text should return that value' => [
        '#DIV/0!',
        '#DIV/0!', 'not found',
    ],
    'A string value containing the #N/A error text should return that value' => [
        '#N/A',
        '#N/A', 'not found',
    ],
    'An Error value should return that error value' => [
        ExcelException::DIV0(),
        ExcelException::DIV0(), 'not found',
    ],
    'NA() value should return the alternative value' => [
        'not found',
        ExcelException::NA(), 'not found',
    ],
];
