<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class PriceMatTest extends TestCase
{
    /**
     * @dataProvider providerPRICEMAT
     *
     * @param mixed $expectedResult
     */
    public function testPRICEMAT($expectedResult, ...$args): void
    {
        $result = Financial\Securities\Price::priceAtMaturity(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPRICEMAT(): array
    {
        return require 'tests/data/Calculation/Financial/PRICEMAT.php';
    }
}
