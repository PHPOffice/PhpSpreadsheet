<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
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
     * @param null|mixed $cellReference
     */
    public function testROW($expectedResult, $cellReference = null): void
    {
        $result = LookupRef::ROW($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public function providerROW(): array
    {
        return require 'tests/data/Calculation/LookupRef/ROW.php';
    }

    public function testROWwithNull(): void
    {
        $cell = $this->getMockBuilder(Cell::class)
            ->onlyMethods(['getRow'])
            ->disableOriginalConstructor()
            ->getMock();
        $cell->method('getRow')
            ->willReturn(3);

        $result = LookupRef::ROW(null, $cell);
        self::assertSame(3, $result);
    }
}
