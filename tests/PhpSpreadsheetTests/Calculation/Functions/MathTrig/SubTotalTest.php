<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PhpOffice\PhpSpreadsheet\Worksheet\RowDimension;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class SubTotalTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSUBTOTAL
     *
     * @param mixed $expectedResult
     */
    public function testSUBTOTAL($expectedResult, ...$args)
    {
        $cell = $this->getMockBuilder(Cell::class)
            ->setMethods(['getValue', 'isFormula'])
            ->disableOriginalConstructor()
            ->getMock();
        $cell->method('getValue')
            ->willReturn(null);
        $cell->method('getValue')
            ->willReturn(false);
        $worksheet = $this->getMockBuilder(Worksheet::class)
            ->setMethods(['cellExists', 'getCell'])
            ->disableOriginalConstructor()
            ->getMock();
        $worksheet->method('cellExists')
            ->willReturn(true);
        $worksheet->method('getCell')
            ->willReturn($cell);
        $cellReference = $this->getMockBuilder(Cell::class)
            ->setMethods(['getWorksheet'])
            ->disableOriginalConstructor()
            ->getMock();
        $cellReference->method('getWorksheet')
            ->willReturn($worksheet);

        array_push($args, $cellReference);
        $result = MathTrig::SUBTOTAL(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSUBTOTAL()
    {
        return require 'data/Calculation/MathTrig/SUBTOTAL.php';
    }

    protected function rowVisibility()
    {
        $data = [1 => false, 2 => true, 3 => false, 4 => true, 5 => false, 6 => false, 7 => false, 8 => true, 9 => false, 10 => true, 11 => true];
        foreach ($data as $k => $v) {
            yield $k => $v;
        }
    }

    /**
     * @dataProvider providerHiddenSUBTOTAL
     *
     * @param mixed $expectedResult
     */
    public function testHiddenSUBTOTAL($expectedResult, ...$args)
    {
        $visibilityGenerator = $this->rowVisibility();

        $rowDimension = $this->getMockBuilder(RowDimension::class)
            ->setMethods(['getVisible'])
            ->disableOriginalConstructor()
            ->getMock();
        $rowDimension->method('getVisible')
            ->will($this->returnCallback(function () use ($visibilityGenerator) {
                $result = $visibilityGenerator->current();
                $visibilityGenerator->next();

                return $result;
            }));
        $columnDimension = $this->getMockBuilder(ColumnDimension::class)
            ->setMethods(['getVisible'])
            ->disableOriginalConstructor()
            ->getMock();
        $columnDimension->method('getVisible')
            ->willReturn(true);
        $cell = $this->getMockBuilder(Cell::class)
            ->setMethods(['getValue', 'isFormula'])
            ->disableOriginalConstructor()
            ->getMock();
        $cell->method('getValue')
            ->willReturn('');
        $cell->method('getValue')
            ->willReturn(false);
        $worksheet = $this->getMockBuilder(Worksheet::class)
            ->setMethods(['cellExists', 'getCell', 'getRowDimension', 'getColumnDimension'])
            ->disableOriginalConstructor()
            ->getMock();
        $worksheet->method('cellExists')
            ->willReturn(true);
        $worksheet->method('getCell')
            ->willReturn($cell);
        $worksheet->method('getRowDimension')
            ->willReturn($rowDimension);
        $worksheet->method('getColumnDimension')
            ->willReturn($columnDimension);
        $cellReference = $this->getMockBuilder(Cell::class)
            ->setMethods(['getWorksheet'])
            ->disableOriginalConstructor()
            ->getMock();
        $cellReference->method('getWorksheet')
            ->willReturn($worksheet);

        array_push($args, $cellReference);
        $result = MathTrig::SUBTOTAL(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerHiddenSUBTOTAL()
    {
        return require 'data/Calculation/MathTrig/SUBTOTALHIDDEN.php';
    }

    protected function cellValues(array $cellValues)
    {
        foreach ($cellValues as $k => $v) {
            yield $k => $v;
        }
    }

    protected function cellIsFormula(array $cellValues)
    {
        foreach ($cellValues as $cellValue) {
            yield $cellValue[0] === '=';
        }
    }

    /**
     * @dataProvider providerNestedSUBTOTAL
     *
     * @param mixed $expectedResult
     */
    public function testNestedSUBTOTAL($expectedResult, ...$args)
    {
        $cellValueGenerator = $this->cellValues(Functions::flattenArray(array_slice($args, 1)));
        $cellIsFormulaGenerator = $this->cellIsFormula(Functions::flattenArray(array_slice($args, 1)));

        $cell = $this->getMockBuilder(Cell::class)
            ->setMethods(['getValue', 'isFormula'])
            ->disableOriginalConstructor()
            ->getMock();
        $cell->method('getValue')
            ->will($this->returnCallback(function () use ($cellValueGenerator) {
                $result = $cellValueGenerator->current();
                $cellValueGenerator->next();

                return $result;
            }));
        $cell->method('isFormula')
            ->will($this->returnCallback(function () use ($cellIsFormulaGenerator) {
                $result = $cellIsFormulaGenerator->current();
                $cellIsFormulaGenerator->next();

                return $result;
            }));
        $worksheet = $this->getMockBuilder(Worksheet::class)
            ->setMethods(['cellExists', 'getCell'])
            ->disableOriginalConstructor()
            ->getMock();
        $worksheet->method('cellExists')
            ->willReturn(true);
        $worksheet->method('getCell')
            ->willReturn($cell);
        $cellReference = $this->getMockBuilder(Cell::class)
            ->setMethods(['getWorksheet'])
            ->disableOriginalConstructor()
            ->getMock();
        $cellReference->method('getWorksheet')
            ->willReturn($worksheet);

        array_push($args, $cellReference);

        $result = MathTrig::SUBTOTAL(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerNestedSUBTOTAL()
    {
        return require 'data/Calculation/MathTrig/SUBTOTALNESTED.php';
    }
}
