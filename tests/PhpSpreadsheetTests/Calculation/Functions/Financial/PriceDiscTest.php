<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class PriceDiscTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPRICEDISC
     *
     * @param mixed $expectedResult
     */
    public function testPRICEDISC($expectedResult, array $args): void
    {
        $result = Financial::PRICEDISC(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPRICEDISC(): array
    {
        return require 'tests/data/Calculation/Financial/PRICEDISC.php';
    }
}
