<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class RowsTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerROWS
     *
     * @param mixed $expectedResult
     */
    public function testROWS($expectedResult, ...$args)
    {
        $result = LookupRef::ROWS(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerROWS()
    {
        return require 'tests/data/Calculation/LookupRef/ROWS.php';
    }
}
