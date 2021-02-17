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
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerSUBTOTAL
     *
     * @param mixed $expectedResult
     */
    public function testSUBTOTAL($expectedResult, ...$args): void
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
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSUBTOTAL()
    {
        return require 'tests/data/Calculation/MathTrig/SUBTOTAL.php';
    }

    protected function rowVisibility($data)
    {
        foreach ($data as $row => $visibility) {
            yield $row => $visibility;
        }
    }

    /**
     * @dataProvider providerHiddenSUBTOTAL
     *
     * @param mixed $expectedResult
     * @param mixed $hiddenRows
     */
    public function testHiddenSUBTOTAL($expectedResult, $hiddenRows, ...$args): void
    {
        $visibilityGenerator = $this->rowVisibility($hiddenRows);

        $rowDimension = $this->getMockBuilder(RowDimension::class)
            ->setMethods(['getVisible'])
            ->disableOriginalConstructor()
            ->getMock();
        $rowDimension->method('getVisible')
            ->willReturnCallback(function () use ($visibilityGenerator) {
                $result = $visibilityGenerator->current();
                $visibilityGenerator->next();

                return $result;
            });
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
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerHiddenSUBTOTAL()
    {
        return require 'tests/data/Calculation/MathTrig/SUBTOTALHIDDEN.php';
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
            yield is_string($cellValue) && $cellValue[0] === '=';
        }
    }

    /**
     * @dataProvider providerNestedSUBTOTAL
     *
     * @param mixed $expectedResult
     */
    public function testNestedSUBTOTAL($expectedResult, ...$args): void
    {
        $cellValueGenerator = $this->cellValues(Functions::flattenArray(array_slice($args, 1)));
        $cellIsFormulaGenerator = $this->cellIsFormula(Functions::flattenArray(array_slice($args, 1)));

        $cell = $this->getMockBuilder(Cell::class)
            ->setMethods(['getValue', 'isFormula'])
            ->disableOriginalConstructor()
            ->getMock();
        $cell->method('getValue')
            ->willReturnCallback(function () use ($cellValueGenerator) {
                $result = $cellValueGenerator->current();
                $cellValueGenerator->next();

                return $result;
            });
        $cell->method('isFormula')
            ->willReturnCallback(function () use ($cellIsFormulaGenerator) {
                $result = $cellIsFormulaGenerator->current();
                $cellIsFormulaGenerator->next();

                return $result;
            });
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
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerNestedSUBTOTAL()
    {
        return require 'tests/data/Calculation/MathTrig/SUBTOTALNESTED.php';
    }
}
