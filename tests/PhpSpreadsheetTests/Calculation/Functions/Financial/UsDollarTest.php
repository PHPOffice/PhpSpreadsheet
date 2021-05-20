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
    public function testUSDOLLAR($expectedResult, ...$args): void
    {
        $result = Dollar::format(...$args);
        self::assertSame($expectedResult, $result);
    }

    public function providerUSDOLLAR(): array
    {
        return require 'tests/data/Calculation/Financial/USDOLLAR.php';
    }
}
