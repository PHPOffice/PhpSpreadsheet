<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class PriceMatTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPRICEMAT
     *
     * @param mixed $expectedResult
     */
    public function testPRICEMAT($expectedResult, ...$args): void
    {
        $result = Financial::PRICEMAT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPRICEMAT(): array
    {
        return require 'tests/data/Calculation/Financial/PRICEMAT.php';
    }
}
