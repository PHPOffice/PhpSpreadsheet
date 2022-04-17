<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Dollar;
use PHPUnit\Framework\TestCase;

class UsDollarTest extends TestCase
{
    /**
     * @dataProvider providerUSDOLLAR
     *
     * @param mixed $expectedResult
     */
    public function testUSDOLLAR($expectedResult, float $amount, ?int $precision = null): void
    {
        if ($precision === null) {
            $result = Dollar::format($amount);
        } else {
            $result = Dollar::format($amount, $precision);
        }
        self::assertSame($expectedResult, $result);
    }

    public function providerUSDOLLAR(): array
    {
        return require 'tests/data/Calculation/Financial/USDOLLAR.php';
    }
}
