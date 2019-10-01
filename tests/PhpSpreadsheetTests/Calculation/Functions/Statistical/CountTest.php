<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CountTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBasicCOUNT
     *
     * @param mixed $expectedResult
     */
    public function testBasicCOUNT($expectedResult, ...$args)
    {
        $result = Statistical::COUNT(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerBasicCOUNT()
    {
        return require 'data/Calculation/Statistical/BasicCOUNT.php';
    }

    /**
     * @dataProvider providerExcelCOUNT
     *
     * @param mixed $expectedResult
     */
    public function testExcelCOUNT($expectedResult, ...$args)
    {
        $result = Statistical::COUNT(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerExcelCOUNT()
    {
        return require 'data/Calculation/Statistical/ExcelCOUNT.php';
    }

    /**
     * @dataProvider providerOpenOfficeCOUNT
     *
     * @param mixed $expectedResult
     */
    public function testOpenOfficeCOUNT($expectedResult, ...$args)
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical::COUNT(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerOpenOfficeCOUNT()
    {
        return require 'data/Calculation/Statistical/OpenOfficeCOUNT.php';
    }

    /**
     * @dataProvider providerGnumericCOUNT
     *
     * @param mixed $expectedResult
     */
    public function testGnumericCOUNT($expectedResult, ...$args)
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);

        $result = Statistical::COUNT(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerGnumericCOUNT()
    {
        return require 'data/Calculation/Statistical/GnumericCOUNT.php';
    }
}
