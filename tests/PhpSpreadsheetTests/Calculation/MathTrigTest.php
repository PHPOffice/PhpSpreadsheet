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
     * @dataProvider providerMROUND
     *
     * @param mixed $expectedResult
     */
    public function testMROUND($expectedResult, ...$args)
    {
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_VALUE);
        $result = MathTrig::MROUND(...$args);
        Calculation::setArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerMROUND()
    {
        return require 'data/Calculation/MathTrig/MROUND.php';
    }

    /**
     * @dataProvider providerPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testPRODUCT($expectedResult, ...$args)
    {
        $result = MathTrig::PRODUCT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerPRODUCT()
    {
        return require 'data/Calculation/MathTrig/PRODUCT.php';
    }

    /**
     * @dataProvider providerQUOTIENT
     *
     * @param mixed $expectedResult
     */
    public function testQUOTIENT($expectedResult, ...$args)
    {
        $result = MathTrig::QUOTIENT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerQUOTIENT()
    {
        return require 'data/Calculation/MathTrig/QUOTIENT.php';
    }

    /**
     * @dataProvider providerROUNDUP
     *
     * @param mixed $expectedResult
     */
    public function testROUNDUP($expectedResult, ...$args)
    {
        $result = MathTrig::ROUNDUP(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerROUNDUP()
    {
        return require 'data/Calculation/MathTrig/ROUNDUP.php';
    }

    /**
     * @dataProvider providerROUNDDOWN
     *
     * @param mixed $expectedResult
     */
    public function testROUNDDOWN($expectedResult, ...$args)
    {
        $result = MathTrig::ROUNDDOWN(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerROUNDDOWN()
    {
        return require 'data/Calculation/MathTrig/ROUNDDOWN.php';
    }

    /**
     * @dataProvider providerSERIESSUM
     *
     * @param mixed $expectedResult
     */
    public function testSERIESSUM($expectedResult, ...$args)
    {
        $result = MathTrig::SERIESSUM(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSERIESSUM()
    {
        return require 'data/Calculation/MathTrig/SERIESSUM.php';
    }

    /**
     * @dataProvider providerSUMSQ
     *
     * @param mixed $expectedResult
     */
    public function testSUMSQ($expectedResult, ...$args)
    {
        $result = MathTrig::SUMSQ(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMSQ()
    {
        return require 'data/Calculation/MathTrig/SUMSQ.php';
    }

    /**
     * @dataProvider providerSUMPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testSUMPRODUCT($expectedResult, ...$args)
    {
        $result = MathTrig::SUMPRODUCT(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSUMPRODUCT()
    {
        return require 'data/Calculation/MathTrig/SUMPRODUCT.php';
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
     * @dataProvider providerTRUNC
     *
     * @param mixed $expectedResult
     */
    public function testTRUNC($expectedResult, ...$args)
    {
        $result = MathTrig::TRUNC(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerTRUNC()
    {
        return require 'data/Calculation/MathTrig/TRUNC.php';
    }

    /**
     * @dataProvider providerROMAN
     *
     * @param mixed $expectedResult
     */
    public function testROMAN($expectedResult, ...$args)
    {
        $result = MathTrig::ROMAN(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerROMAN()
    {
        return require 'data/Calculation/MathTrig/ROMAN.php';
    }

    /**
     * @dataProvider providerSQRTPI
     *
     * @param mixed $expectedResult
     */
    public function testSQRTPI($expectedResult, ...$args)
    {
        $result = MathTrig::SQRTPI(...$args);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSQRTPI()
    {
        return require 'data/Calculation/MathTrig/SQRTPI.php';
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

    /**
     * @dataProvider providerSEC
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testSEC($expectedResult, $angle)
    {
        $result = MathTrig::SEC($angle);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSEC()
    {
        return require 'data/Calculation/MathTrig/SEC.php';
    }

    /**
     * @dataProvider providerSECH
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testSECH($expectedResult, $angle)
    {
        $result = MathTrig::SECH($angle);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerSECH()
    {
        return require 'data/Calculation/MathTrig/SECH.php';
    }

    /**
     * @dataProvider providerCSC
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCSC($expectedResult, $angle)
    {
        $result = MathTrig::CSC($angle);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCSC()
    {
        return require 'data/Calculation/MathTrig/CSC.php';
    }

    /**
     * @dataProvider providerCSCH
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCSCH($expectedResult, $angle)
    {
        $result = MathTrig::CSCH($angle);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCSCH()
    {
        return require 'data/Calculation/MathTrig/CSCH.php';
    }

    /**
     * @dataProvider providerCOT
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCOT($expectedResult, $angle)
    {
        $result = MathTrig::COT($angle);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCOT()
    {
        return require 'data/Calculation/MathTrig/COT.php';
    }

    /**
     * @dataProvider providerCOTH
     *
     * @param mixed $expectedResult
     * @param mixed $angle
     */
    public function testCOTH($expectedResult, $angle)
    {
        $result = MathTrig::COTH($angle);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerCOTH()
    {
        return require 'data/Calculation/MathTrig/COTH.php';
    }

    /**
     * @dataProvider providerACOT
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testACOT($expectedResult, $number)
    {
        $result = MathTrig::ACOT($number);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerACOT()
    {
        return require 'data/Calculation/MathTrig/ACOT.php';
    }

    /**
     * @dataProvider providerACOTH
     *
     * @param mixed $expectedResult
     * @param mixed $number
     */
    public function testACOTH($expectedResult, $number)
    {
        $result = MathTrig::ACOTH($number);
        self::assertEquals($expectedResult, $result, null, 1E-12);
    }

    public function providerACOTH()
    {
        return require 'data/Calculation/MathTrig/ACOTH.php';
    }
}
