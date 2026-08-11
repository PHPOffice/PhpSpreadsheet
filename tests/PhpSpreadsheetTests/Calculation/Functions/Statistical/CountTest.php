<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

class CountTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerBasicCOUNT')]
    public function testBasicCOUNT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCaseNoBracket('COUNT', $expectedResult, ...$args);
    }

    public static function providerBasicCOUNT(): array
    {
        return require 'tests/data/Calculation/Statistical/BasicCOUNT.php';
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('providerExcelCOUNT')]
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

    #[\PHPUnit\Framework\Attributes\DataProvider('providerOpenOfficeCOUNT')]
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

    #[\PHPUnit\Framework\Attributes\DataProvider('providerGnumericCOUNT')]
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
