<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

return [
    'Empty Value' => [
        ExcelException::NAME(),
    ],
    'Null Value' => [
        ExcelException::NAME(),
        null,
    ],
    'Negative Integer' => [
        false,
        -1,
    ],
    'Zero Value' => [
        true,
        0,
    ],
    'Positive Integer' => [
        false,
        9,
    ],
    'Odd Float #1' => [
        false,
        1.25,
    ],
    'Odd Float #2' => [
        false,
        1.5,
    ],
    'Even Float #1' => [
        true,
        2.25,
    ],
    'Even Float #2' => [
        true,
        2.5,
    ],
    'Empty String' => [
        ExcelException::VALUE(),
        '',
    ],
    'String containing Negative Odd Integer' => [
        false,
        '-1',
    ],
    'String containing Positive Even Integer' => [
        true,
        '2',
    ],
    'String containing Negative Odd Float' => [
        false,
        '-1.5',
    ],
    'String containing Positive Even Float' => [
        true,
        '2.5',
    ],
    'Non-numeric String' => [
        ExcelException::VALUE(),
        'ABC',
    ],
    'VALUE Exception' => [
        ExcelException::VALUE(),
        ExcelException::VALUE(),
    ],
    'NA Exception' => [
        ExcelException::NA(),
        ExcelException::NA(),
    ],
    'String containing TRUE text' => [
        ExcelException::VALUE(),
        'TRUE',
    ],
    'Boolean True' => [
        ExcelException::VALUE(),
        true,
    ],
    'Boolean False' => [
        ExcelException::VALUE(),
        false,
    ],
];
