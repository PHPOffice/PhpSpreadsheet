<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PHPUnit\Framework\TestCase;

class DataTypeTest extends TestCase
{
    public function testGetErrorCodes()
    {
        $result = DataType::getErrorCodes();
        self::assertInternalType('array', $result);
        self::assertGreaterThan(0, count($result));
        self::assertArrayHasKey('#NULL!', $result);
    }
}
