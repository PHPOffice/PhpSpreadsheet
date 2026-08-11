<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PHPUnit\Framework\Attributes\DataProvider;

class PriceDiscTest extends AllSetupTeardown
{
    /** @param mixed[] $args */
    #[DataProvider('providerPRICEDISC')]
    public function testPRICEDISC(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('PRICEDISC', $expectedResult, $args);
    }

    public static function providerPRICEDISC(): array
    {
        return require 'tests/data/Calculation/Financial/PRICEDISC.php';
    }
}
