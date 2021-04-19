<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class CumPrincTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCUMPRINC
     *
     * @param mixed $expectedResult
     */
    public function testCUMPRINC($expectedResult, ...$args): void
    {
        $result = Financial::CUMPRINC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCUMPRINC(): array
    {
        return require 'tests/data/Calculation/Financial/CUMPRINC.php';
    }
}
