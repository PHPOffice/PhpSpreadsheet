<?php

use PhpOffice\PhpSpreadsheet\Calculation\Functions;

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
        Functions::REF(),
        null,
        null,
    ],
    [
        Functions::REF(),
        '',
        null,
    ],
];
