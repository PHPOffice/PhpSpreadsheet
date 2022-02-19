<?php

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
        '=SUM(A1:A10)',
        '=SUM(A1:A10)',
        DataType::TYPE_FORMULA,
    ],
    [
        44596.562789351854,
        '2022-02-04 13:30:25',
        DataType::TYPE_ISO_DATE,
    ],
    [
        '#DIV/0!',
        '#DIV/0!',
        DataType::TYPE_ERROR,
    ],
    [
        '#NULL!',
        'NOT A VALID ERROR TYPE VALUE',
        DataType::TYPE_ERROR,
    ],
];
