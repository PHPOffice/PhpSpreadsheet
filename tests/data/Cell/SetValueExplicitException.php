<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Cell\DataType;

return [
    'invalid numeric' => ['XYZ', DataType::TYPE_NUMERIC],
    'invalid array' => [[], DataType::TYPE_STRING],
    'invalid unstringable object' => [new DateTime(), DataType::TYPE_INLINE],
];
