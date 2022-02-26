<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

return [
    [
        1234567890123456789,
        '01234567890123456789',
        DataType::TYPE_NUMERIC,
    ],
    [
        1234567890123456789,
        1234567890123456789,
        DataType::TYPE_NUMERIC,
    ],
    [
        123.456,
        '123.456',
        DataType::TYPE_NUMERIC,
    ],
    [
        123.456,
        123.456,
        DataType::TYPE_NUMERIC,
    ],
    [
        0,
        null,
        DataType::TYPE_NUMERIC,
    ],
    [
        0,
        false,
        DataType::TYPE_NUMERIC,
    ],
    [
        1,
        true,
        DataType::TYPE_NUMERIC,
    ],
    [
        44613.43090277778,
        '2022-02-21 10:20:30',
        DataType::TYPE_ISO_DATE,
    ],
    [
        44613.0,
        '2022-02-21',
        DataType::TYPE_ISO_DATE,
    ],
    [
        -30879.0,
        '1815-06-15',               // Dates outside the Excel Range should fail really
        DataType::TYPE_ISO_DATE,
    ],
    [
        ExcelError::DIV0(),
        '#DIV/0!',
        DataType::TYPE_ERROR,
    ],
    [
        ExcelError::null(),
        'NOT A VALID ERROR TYPE VALUE',
        DataType::TYPE_ERROR,
    ],
];
