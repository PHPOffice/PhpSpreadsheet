<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;

return [
    [
        'XYZ',
        DataType::TYPE_NUMERIC,
    ],
    [
        44596,
        DataType::TYPE_ISO_DATE,
    ],
    [
        false,
        DataType::TYPE_ISO_DATE,
    ],
    [
        'ABCD-EF-GH II:JJ:KK',
        DataType::TYPE_ISO_DATE,
    ],
    [
        1234,
        'INVALID DATATYPE',
    ],
];
