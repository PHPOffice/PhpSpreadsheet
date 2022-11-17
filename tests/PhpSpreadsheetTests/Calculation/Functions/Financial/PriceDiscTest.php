<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class PriceDiscTest extends TestCase
{
    /**
     * @dataProvider providerPRICEDISC
     *
     * @param mixed $expectedResult
     */
    public function testPRICEDISC($expectedResult, array $args): void
    {
        $result = Financial\Securities\Price::priceDiscounted(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPRICEDISC(): array
    {
        return require 'tests/data/Calculation/Financial/PRICEDISC.php';
    }
}
