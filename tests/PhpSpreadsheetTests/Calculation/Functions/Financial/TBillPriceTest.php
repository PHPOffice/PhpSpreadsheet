<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class TBillPriceTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerTBILLPRICE
     *
     * @param mixed $expectedResult
     */
    public function testTBILLPRICE($expectedResult, ...$args): void
    {
        $result = Financial::TBILLPRICE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTBILLPRICE(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLPRICE.php';
    }
}
