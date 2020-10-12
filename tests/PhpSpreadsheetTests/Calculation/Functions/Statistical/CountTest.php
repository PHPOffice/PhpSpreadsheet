<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CountTest extends TestCase
{
    private $compatibilityMode;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    /**
     * @dataProvider providerBasicCOUNT
     *
     * @param mixed $expectedResult
     */
    public function testBasicCOUNT($expectedResult, ...$args): void
    {
        $result = Statistical::COUNT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
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
    public function testExcelCOUNT($expectedResult, ...$args): void
    {
        $result = Statistical::COUNT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
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
    public function testOpenOfficeCOUNT($expectedResult, ...$args): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical::COUNT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
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
    public function testGnumericCOUNT($expectedResult, ...$args): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_GNUMERIC);

        $result = Statistical::COUNT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerGnumericCOUNT()
    {
        return require 'tests/data/Calculation/Statistical/GnumericCOUNT.php';
    }
}
