<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Cell\DataType;

return [
    'invalid numeric' => ['XYZ', DataType::TYPE_NUMERIC, 'Invalid numeric value'],
    'invalid array' => [[], DataType::TYPE_STRING, 'Unable to convert to string'],
    'invalid unstringable object' => [new DateTime(), DataType::TYPE_INLINE, 'Unable to convert to string'],
];
