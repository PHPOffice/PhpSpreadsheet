<?php

use PhpOffice\PhpSpreadsheet\Calculation\Information\ExcelError;

return [
    [
        ['https://phpspreadsheet.readthedocs.io/en/latest/', 'https://phpspreadsheet.readthedocs.io/en/latest/'],
        'https://phpspreadsheet.readthedocs.io/en/latest/',
        null,
    ],
    [
        ['https://phpspreadsheet.readthedocs.io/en/latest/', 'Read the Docs'],
        'https://phpspreadsheet.readthedocs.io/en/latest/',
        'Read the Docs',
    ],
    [
        'exception',
        null,
        null,
    ],
    [
        ExcelError::REF(),
        '',
        null,
    ],
];
