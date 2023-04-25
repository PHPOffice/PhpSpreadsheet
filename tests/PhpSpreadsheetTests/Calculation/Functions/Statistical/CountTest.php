<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class CountTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerBasicCOUNT
     *
     * @param mixed $expectedResult
     */
    public function testBasicCOUNT($expectedResult, ...$args): void
    {
        $this->runTestCaseNoBracket('COUNT', $expectedResult, ...$args);
    }

    public static function providerBasicCOUNT(): array
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
        if (is_array($args[0])) {
            $this->runTestCaseNoBracket('COUNT', $expectedResult, ...$args);
        } else {
            $this->runTestCaseDirect('COUNT', $expectedResult, ...$args);
        }
    }

    public static function providerExcelCOUNT(): array
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
        $this->setOpenOffice();
        if (is_array($args[0])) {
            $this->runTestCaseNoBracket('COUNT', $expectedResult, ...$args);
        } else {
            $this->runTestCaseDirect('COUNT', $expectedResult, ...$args);
        }
    }

    public static function providerOpenOfficeCOUNT(): array
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
        $this->setGnumeric();
        $this->runTestCaseNoBracket('COUNT', $expectedResult, ...$args);
    }

    public static function providerGnumericCOUNT(): array
    {
        return require 'tests/data/Calculation/Statistical/GnumericCOUNT.php';
    }
}
