<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class CountTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerBasicCOUNT
     */
    public function testBasicCOUNT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('COUNT', $expectedResult, ...$args);
    }

    public static function providerBasicCOUNT(): array
    {
        return require 'tests/data/Calculation/Statistical/BasicCOUNT.php';
    }

    /**
     * @dataProvider providerExcelCOUNT
     */
    public function testExcelCOUNT(mixed $expectedResult, mixed ...$args): void
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
     */
    public function testOpenOfficeCOUNT(mixed $expectedResult, mixed ...$args): void
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
     */
    public function testGnumericCOUNT(mixed $expectedResult, mixed ...$args): void
    {
        $this->setGnumeric();
        $this->runTestCaseNoBracket('COUNT', $expectedResult, ...$args);
    }

    public static function providerGnumericCOUNT(): array
    {
        return require 'tests/data/Calculation/Statistical/GnumericCOUNT.php';
    }
}
