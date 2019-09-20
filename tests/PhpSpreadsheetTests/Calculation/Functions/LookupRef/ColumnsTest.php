<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class ColumnsTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOLUMNS
     *
     * @param mixed $expectedResult
     */
    public function testCOLUMNS($expectedResult, ...$args)
    {
        $result = LookupRef::COLUMNS(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerCOLUMNS()
    {
        return require 'data/Calculation/LookupRef/COLUMNS.php';
    }
}
