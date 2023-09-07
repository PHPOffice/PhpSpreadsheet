<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class PriceDiscTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerPRICEDISC
     */
    public function testPRICEDISC(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('PRICEDISC', $expectedResult, $args);
    }

    public static function providerPRICEDISC(): array
    {
        return require 'tests/data/Calculation/Financial/PRICEDISC.php';
    }
}
