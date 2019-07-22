<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PhpOffice\PhpSpreadsheet\Worksheet\RowDimension;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class MathTrigTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSUMX2MY2
     *
     * @param mixed $expectedResult
     */
    public function testSUMX2MY2($expectedResult, ...$args)
    {
        $result = MathTrig::SUMX2MY2(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMX2MY2()
    {
        return require 'data/Calculation/MathTrig/SUMX2MY2.php';
    }

    /**
     * @dataProvider providerSUMX2PY2
     *
     * @param mixed $expectedResult
     */
    public function testSUMX2PY2($expectedResult, ...$args)
    {
        $result = MathTrig::SUMX2PY2(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMX2PY2()
    {
        return require 'data/Calculation/MathTrig/SUMX2PY2.php';
    }

    /**
     * @dataProvider providerSUMXMY2
     *
     * @param mixed $expectedResult
     */
    public function testSUMXMY2($expectedResult, ...$args)
    {
        $result = MathTrig::SUMXMY2(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMXMY2()
    {
        return require 'data/Calculation/MathTrig/SUMXMY2.php';
    }

    /**
     * @dataProvider providerSUMIF
     *
     * @param mixed $expectedResult
     */
    public function testSUMIF($expectedResult, ...$args)
    {
        $result = MathTrig::SUMIF(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSUMIF()
    {
        return require 'data/Calculation/MathTrig/SUMIF.php';
    }

    /**
     * @dataProvider providerSUMIFS
     *
     * @param mixed $expectedResult
     */
    public function testSUMIFS($expectedResult, ...$args)
    {
        $result = MathTrig::SUMIFS(...$args);
        self::assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerSUMIFS()
    {
        return require 'data/Calculation/MathTrig/SUMIFS.php';
    }

    /**
     * @dataProvider providerSUBTOTAL
     *
     * @param mixed $expectedResult
     */
    public function testSUBTOTAL($expectedResult, ...$args)
    {
        $cell = $this->getMockBuilder(Cell::class)
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $cell->method('getValue')
            ->willReturn(null);
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
        self::assertEquals($expectedResult, $result, null, 1E-12);
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
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMock();
        $cell->method('getValue')
            ->willReturn('');
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
        self::assertEquals($expectedResult, $result, null, 1E-12);
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
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerNestedSUBTOTAL()
    {
        return require 'data/Calculation/MathTrig/SUBTOTALNESTED.php';
    }
}
