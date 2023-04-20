<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PriceMatTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPRICEMAT
     *
     * @param mixed $expectedResult
     */
    public function testPRICEMAT($expectedResult, ...$args): void
    {
        $this->runTestCase('PRICEMAT', $expectedResult, $args);
    }

    public static function providerPRICEMAT(): array
    {
        return require 'tests/data/Calculation/Financial/PRICEMAT.php';
    }
}
