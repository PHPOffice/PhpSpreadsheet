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
     * @dataProvider providerHLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testHLOOKUP($expectedResult, ...$args)
    {
        $result = LookupRef::HLOOKUP(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerHLOOKUP()
    {
        return require 'data/Calculation/LookupRef/HLOOKUP.php';
    }

    /**
     * @dataProvider providerVLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testVLOOKUP($expectedResult, ...$args)
    {
        $result = LookupRef::VLOOKUP(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerVLOOKUP()
    {
        return require 'data/Calculation/LookupRef/VLOOKUP.php';
    }

    /**
     * @dataProvider providerLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testLOOKUP($expectedResult, ...$args)
    {
        $result = LookupRef::LOOKUP(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLOOKUP()
    {
        return require 'data/Calculation/LookupRef/LOOKUP.php';
    }

    /**
     * @dataProvider providerMATCH
     *
     * @param mixed $expectedResult
     */
    public function testMATCH($expectedResult, ...$args)
    {
        $result = LookupRef::MATCH(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMATCH()
    {
        return require 'data/Calculation/LookupRef/MATCH.php';
    }

    /**
     * @dataProvider providerINDEX
     *
     * @param mixed $expectedResult
     */
    public function testINDEX($expectedResult, ...$args)
    {
        $result = LookupRef::INDEX(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerINDEX()
    {
        return require 'data/Calculation/LookupRef/INDEX.php';
    }

    /**
     * @dataProvider providerCOLUMNS
     *
     * @param mixed $expectedResult
     */
    public function testCOLUMNS($expectedResult, ...$args)
    {
        $result = LookupRef::COLUMNS(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCOLUMNS()
    {
        return require 'data/Calculation/LookupRef/COLUMNS.php';
    }

    /**
     * @dataProvider providerROWS
     *
     * @param mixed $expectedResult
     */
    public function testROWS($expectedResult, ...$args)
    {
        $result = LookupRef::ROWS(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerROWS()
    {
        return require 'data/Calculation/LookupRef/ROWS.php';
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
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerFormulaText()
    {
        return require 'data/Calculation/LookupRef/FORMULATEXT.php';
    }
}
