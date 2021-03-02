<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCOLUMN
     *
     * @param mixed $expectedResult
     */
    public function testCOLUMN($expectedResult, string $cellReference): void
    {
        $result = LookupRef::COLUMN($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public function providerCOLUMN()
    {
        return require 'tests/data/Calculation/LookupRef/COLUMN.php';
    }
}
