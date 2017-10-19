<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PHPUnit_Framework_TestCase;

class DataTypeTest extends PHPUnit_Framework_TestCase
{
    public function testGetErrorCodes()
    {
        $result = DataType::getErrorCodes();
        self::assertInternalType('array', $result);
        self::assertGreaterThan(0, count($result));
        self::assertArrayHasKey('#NULL!', $result);
    }
}
