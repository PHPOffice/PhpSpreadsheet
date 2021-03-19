<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerROW
     *
     * @param mixed $expectedResult
     */
    public function testROW($expectedResult, string $cellReference): void
    {
        $result = LookupRef::ROW($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public function providerROW()
    {
        return require 'tests/data/Calculation/LookupRef/ROW.php';
    }
}
