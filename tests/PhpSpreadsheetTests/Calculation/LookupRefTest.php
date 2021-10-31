<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

/**
 * Class LookupRefTest.
 */
class LookupRefTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerFormulaText
     *
     * @param mixed $expectedResult
     * @param mixed $reference       Reference to the cell we wish to test
     * @param mixed $value           Value of the cell we wish to test
     */
    public function testFormulaText($expectedResult, $reference, $value = 'undefined'): void
    {
        $ourCell = null;
        if ($value !== 'undefined') {
            $remoteCell = $this->getMockBuilder(Cell::class)
                ->disableOriginalConstructor()
                ->getMock();
            $remoteCell->method('isFormula')
                ->willReturn(substr($value, 0, 1) == '=');
            $remoteCell->method('getValue')
                ->willReturn($value);

            $remoteSheet = $this->getMockBuilder(Worksheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $remoteSheet->method('cellExists')
                ->willReturn(true);
            $remoteSheet->method('getCell')
                ->willReturn($remoteCell);

            $workbook = $this->getMockBuilder(Spreadsheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $workbook->method('getSheetByName')
                ->willReturn($remoteSheet);

            $sheet = $this->getMockBuilder(Worksheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $sheet->method('cellExists')
                ->willReturn(true);
            $sheet->method('getCell')
                ->willReturn($remoteCell);
            $sheet->method('getParent')
                ->willReturn($workbook);

            $ourCell = $this->getMockBuilder(Cell::class)
                ->disableOriginalConstructor()
                ->getMock();
            $ourCell->method('getWorksheet')
                ->willReturn($sheet);
        }

        $result = LookupRef::FORMULATEXT($reference, $ourCell);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerFormulaText(): array
    {
        return require 'tests/data/Calculation/LookupRef/FORMULATEXT.php';
    }

    public function testFormulaTextWithoutCell(): void
    {
        $result = LookupRef::FORMULATEXT('A1');
        self::assertEquals(Functions::REF(), $result);
    }
}
