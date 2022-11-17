<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class IntRateTest extends TestCase
{
    /**
     * @dataProvider providerINTRATE
     *
     * @param mixed $expectedResult
     */
    public function testINTRATE($expectedResult, ...$args): void
    {
        $result = Financial\Securities\Rates::interest(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerINTRATE(): array
    {
        return require 'tests/data/Calculation/Financial/INTRATE.php';
    }
}
