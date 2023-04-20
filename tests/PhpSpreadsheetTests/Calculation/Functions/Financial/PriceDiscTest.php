<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PriceDiscTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPRICEDISC
     *
     * @param mixed $expectedResult
     */
    public function testPRICEDISC($expectedResult, array $args): void
    {
        $this->runTestCase('PRICEDISC', $expectedResult, $args);
    }

    public static function providerPRICEDISC(): array
    {
        return require 'tests/data/Calculation/Financial/PRICEDISC.php';
    }
}
