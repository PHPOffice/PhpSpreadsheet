<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PriceMatTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPRICEMAT
     */
    public function testPRICEMAT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('PRICEMAT', $expectedResult, $args);
    }

    public static function providerPRICEMAT(): array
    {
        return require 'tests/data/Calculation/Financial/PRICEMAT.php';
    }
}
