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
    public function setUp()
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
    public function testFormulaText($expectedResult, $reference, $value = 'undefined')
    {
        $ourCell = null;
        if ($value !== 'undefined') {
            $remoteCell = $this->getMockBuilder(Cell::class)
                ->disableOriginalConstructor()
                ->getMock();
            $remoteCell->method('isFormula')
                ->will($this->returnValue(substr($value, 0, 1) == '='));
            $remoteCell->method('getValue')
                ->will($this->returnValue($value));

            $remoteSheet = $this->getMockBuilder(Worksheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $remoteSheet->method('getCell')
                ->will($this->returnValue($remoteCell));

            $workbook = $this->getMockBuilder(Spreadsheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $workbook->method('getSheetByName')
                ->will($this->returnValue($remoteSheet));

            $sheet = $this->getMockBuilder(Worksheet::class)
                ->disableOriginalConstructor()
                ->getMock();
            $sheet->method('getCell')
                ->will($this->returnValue($remoteCell));
            $sheet->method('getParent')
                ->will($this->returnValue($workbook));

            $ourCell = $this->getMockBuilder(Cell::class)
                ->disableOriginalConstructor()
                ->getMock();
            $ourCell->method('getWorksheet')
                ->will($this->returnValue($sheet));
        }

        $result = LookupRef::FORMULATEXT($reference, $ourCell);
        self::assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerFormulaText()
    {
        return require 'data/Calculation/LookupRef/FORMULATEXT.php';
    }
}
