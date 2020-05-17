<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CountTest extends TestCase
{
    protected function setUp(): void
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
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerBasicCOUNT()
    {
        return require 'tests/data/Calculation/Statistical/BasicCOUNT.php';
    }

    /**
     * @dataProvider providerExcelCOUNT
     *
     * @param mixed $expectedResult
     */
    public function testExcelCOUNT($expectedResult, ...$args)
    {
        $result = Statistical::COUNT(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerExcelCOUNT()
    {
        return require 'tests/data/Calculation/Statistical/ExcelCOUNT.php';
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
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOpenOfficeCOUNT()
    {
        return require 'tests/data/Calculation/Statistical/OpenOfficeCOUNT.php';
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
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGnumericCOUNT()
    {
        return require 'tests/data/Calculation/Statistical/GnumericCOUNT.php';
    }
}
