<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class TBillYieldTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerTBILLYIELD
     *
     * @param mixed $expectedResult
     */
    public function testTBILLYIELD($expectedResult, ...$args): void
    {
        $result = Financial::TBILLYIELD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTBILLYIELD(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLYIELD.php';
    }
}
