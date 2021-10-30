<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
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
     * @param null|mixed $cellReference
     */
    public function testCOLUMN($expectedResult, $cellReference = null): void
    {
        $result = LookupRef::COLUMN($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public function providerCOLUMN(): array
    {
        return require 'tests/data/Calculation/LookupRef/COLUMN.php';
    }

    public function testCOLUMNwithNull(): void
    {
        $cell = $this->getMockBuilder(Cell::class)
            ->onlyMethods(['getColumn'])
            ->disableOriginalConstructor()
            ->getMock();
        $cell->method('getColumn')
            ->willReturn('D');

        $result = LookupRef::COLUMN(null, $cell);
        self::assertSame(4, $result);
    }
}
