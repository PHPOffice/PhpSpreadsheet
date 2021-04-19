<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class TBillEqTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerTBILLEQ
     *
     * @param mixed $expectedResult
     */
    public function testTBILLEQ($expectedResult, ...$args): void
    {
        $result = Financial::TBILLEQ(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTBILLEQ(): array
    {
        return require 'tests/data/Calculation/Financial/TBILLEQ.php';
    }
}
