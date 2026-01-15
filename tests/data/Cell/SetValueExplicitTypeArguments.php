<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Cell\DataType;

// This member will be used to validate both:
// Cell::setValueExplicit
// Worksheet::setCellValueExplicit

return [
    'string with default type' => ['default string', null, 'default string', DataType::TYPE_STRING],
    'integer with default type' => [42, null, '42', DataType::TYPE_STRING],
    'string with string' => ['explicit string', DataType::TYPE_STRING, 'explicit string', DataType::TYPE_STRING],
    'integer with string type' => [123, DataType::TYPE_STRING, '123', DataType::TYPE_STRING],
    'numeric string with string type' => ['496', DataType::TYPE_STRING, '496', DataType::TYPE_STRING],
    'integer with numeric type' => [591, DataType::TYPE_NUMERIC, 591, DataType::TYPE_NUMERIC],
    'numeric string with numeric type' => ['1887', DataType::TYPE_NUMERIC, 1887, DataType::TYPE_NUMERIC],
    'true with bool type' => [true, DataType::TYPE_BOOL, true, DataType::TYPE_BOOL],
    'false with bool type' => [false, DataType::TYPE_BOOL, false, DataType::TYPE_BOOL],
    'ISO Date' => ['1996-01-01', DataType::TYPE_ISO_DATE, 35065.0, DataType::TYPE_NUMERIC],
    'Valid Error' => ['#VALUE!', DataType::TYPE_ERROR, '#VALUE!', DataType::TYPE_ERROR],
    'Invalid Error' => ['VALUE!', DataType::TYPE_ERROR, '#NULL!', DataType::TYPE_ERROR],
];
