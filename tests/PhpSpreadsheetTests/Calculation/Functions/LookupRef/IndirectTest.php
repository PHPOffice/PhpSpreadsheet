<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PHPUnit\Framework\TestCase;

class IndirectTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerINDIRECT
     *
     * @param mixed $expectedResult
     * @param null|mixed $cellReference
     */
    public function testINDIRECT($expectedResult, $cellReference = null): void
    {
//        $calculation = $this->getMockBuilder(Calculation::class)
//            ->setMethods(['getInstance', 'extractCellRange'])
//            ->disableOriginalConstructor()
//            ->getMock();
//        $calculation->method('getInstance')
//            ->willReturn($calculation);
//        $calculation->method('extractCellRange')
//            ->willReturn([]);
//
//        $worksheet = $this->getMockBuilder(Cell::class)
//            ->setMethods(['getParent'])
//            ->disableOriginalConstructor()
//            ->getMock();
//
//        $cell = $this->getMockBuilder(Cell::class)
//            ->setMethods(['getWorksheet'])
//            ->disableOriginalConstructor()
//            ->getMock();
//        $cell->method('getWorksheet')
//            ->willReturn($worksheet);

        $result = LookupRef::INDIRECT($cellReference);
        self::assertSame($expectedResult, $result);
    }

    public function providerINDIRECT(): array
    {
        return require 'tests/data/Calculation/LookupRef/INDIRECT.php';
    }
}
