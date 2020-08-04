<?php

use PhpOffice\PhpSpreadsheet\Calculation\ExcelException;

return [
    [
        55,
        55, 'not found',
    ],
    [
        'not found',
        ExcelException::NA(), 'not found',
    ],
];
