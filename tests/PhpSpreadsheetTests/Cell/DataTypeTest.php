<?php

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\DataType;

class DataTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testGetErrorCodes()
    {
        $result = DataType::getErrorCodes();
        $this->assertInternalType('array', $result);
        $this->assertGreaterThan(0, count($result));
        $this->assertArrayHasKey('#NULL!', $result);
    }
}
