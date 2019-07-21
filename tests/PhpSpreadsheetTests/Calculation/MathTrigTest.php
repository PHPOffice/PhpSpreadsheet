<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
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
     * @dataProvider providerATAN2
     *
     * @param mixed $expectedResult
     */
    public function testATAN2($expectedResult, ...$args)
    {
        $result = MathTrig::ATAN2(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerATAN2()
    {
        return require 'data/Calculation/MathTrig/ATAN2.php';
    }

    /**
     * @dataProvider providerCEILING
     *
     * @param mixed $expectedResult
     */
    public function testCEILING($expectedResult, ...$args)
    {
        $result = MathTrig::CEILING(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCEILING()
    {
        return require 'data/Calculation/MathTrig/CEILING.php';
    }

    /**
     * @dataProvider providerCOMBIN
     *
     * @param mixed $expectedResult
     */
    public function testCOMBIN($expectedResult, ...$args)
    {
        $result = MathTrig::COMBIN(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCOMBIN()
    {
        return require 'data/Calculation/MathTrig/COMBIN.php';
    }

    /**
     * @dataProvider providerEVEN
     *
     * @param mixed $expectedResult
     */
    public function testEVEN($expectedResult, ...$args)
    {
        $result = MathTrig::EVEN(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerEVEN()
    {
        return require 'data/Calculation/MathTrig/EVEN.php';
    }

    /**
     * @dataProvider providerODD
     *
     * @param mixed $expectedResult
     */
    public function testODD($expectedResult, ...$args)
    {
        $result = MathTrig::ODD(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerODD()
    {
        return require 'data/Calculation/MathTrig/ODD.php';
    }

    /**
     * @dataProvider providerFACT
     *
     * @param mixed $expectedResult
     */
    public function testFACT($expectedResult, ...$args)
    {
        $result = MathTrig::FACT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFACT()
    {
        return require 'data/Calculation/MathTrig/FACT.php';
    }

    /**
     * @dataProvider providerFACTDOUBLE
     *
     * @param mixed $expectedResult
     */
    public function testFACTDOUBLE($expectedResult, ...$args)
    {
        $result = MathTrig::FACTDOUBLE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFACTDOUBLE()
    {
        return require 'data/Calculation/MathTrig/FACTDOUBLE.php';
    }

    /**
     * @dataProvider providerFLOOR
     *
     * @param mixed $expectedResult
     */
    public function testFLOOR($expectedResult, ...$args)
    {
        $result = MathTrig::FLOOR(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerFLOOR()
    {
        return require 'data/Calculation/MathTrig/FLOOR.php';
    }

    /**
     * @dataProvider providerGCD
     *
     * @param mixed $expectedResult
     */
    public function testGCD($expectedResult, ...$args)
    {
        $result = MathTrig::GCD(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerGCD()
    {
        return require 'data/Calculation/MathTrig/GCD.php';
    }

    /**
     * @dataProvider providerLCM
     *
     * @param mixed $expectedResult
     */
    public function testLCM($expectedResult, ...$args)
    {
        $result = MathTrig::LCM(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerLCM()
    {
        return require 'data/Calculation/MathTrig/LCM.php';
    }

    /**
     * @dataProvider providerINT
     *
     * @param mixed $expectedResult
     */
    public function testINT($expectedResult, ...$args)
    {
        $result = MathTrig::INT(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerINT()
    {
        return require 'data/Calculation/MathTrig/INT.php';
    }

    /**
     * @dataProvider providerSIGN
     *
     * @param mixed $expectedResult
     */
    public function testSIGN($expectedResult, ...$args)
    {
        $result = MathTrig::SIGN(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSIGN()
    {
        return require 'data/Calculation/MathTrig/SIGN.php';
    }

    /**
     * @dataProvider providerPOWER
     *
     * @param mixed $expectedResult
     */
    public function testPOWER($expectedResult, ...$args)
    {
        $result = MathTrig::POWER(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerPOWER()
    {
        return require 'data/Calculation/MathTrig/POWER.php';
    }

    /**
     * @dataProvider providerLOG
     *
     * @param mixed $expectedResult
     */
    public function testLOG($expectedResult, ...$args)
    {
        $result = MathTrig::logBase(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerLOG()
    {
        return require 'data/Calculation/MathTrig/LOG.php';
    }

    /**
     * @dataProvider providerMOD
     *
     * @param mixed $expectedResult
     */
    public function testMOD($expectedResult, ...$args)
    {
        $result = MathTrig::MOD(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMOD()
    {
        return require 'data/Calculation/MathTrig/MOD.php';
    }

    /**
     * @dataProvider providerMDETERM
     *
     * @param mixed $expectedResult
     */
    public function testMDETERM($expectedResult, ...$args)
    {
        $result = MathTrig::MDETERM(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMDETERM()
    {
        return require 'data/Calculation/MathTrig/MDETERM.php';
    }

    /**
     * @dataProvider providerMINVERSE
     *
     * @param mixed $expectedResult
     */
    public function testMINVERSE($expectedResult, ...$args)
    {
        $result = MathTrig::MINVERSE(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerMINVERSE()
    {
        return require 'data/Calculation/MathTrig/MINVERSE.php';
    }

    /**
     * @dataProvider providerMMULT
     *
     * @param mixed $expectedResult
     */
    public function testMMULT($expectedResult, ...$args)
    {
        $result = MathTrig::MMULT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-8);
    }

    public function providerMMULT()
    {
        return require 'data/Calculation/MathTrig/MMULT.php';
    }

    /**
     * @dataProvider providerMULTINOMIAL
     *
     * @param mixed $expectedResult
     */
    public function testMULTINOMIAL($expectedResult, ...$args)
    {
        $result = MathTrig::MULTINOMIAL(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMULTINOMIAL()
    {
        return require 'data/Calculation/MathTrig/MULTINOMIAL.php';
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
