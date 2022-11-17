<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class DiscTest extends TestCase
{
    /**
     * @dataProvider providerDISC
     *
     * @param mixed $expectedResult
     */
    public function testDISC($expectedResult, ...$args): void
    {
        $result = Financial\Securities\Rates::discount(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDISC(): array
    {
        return require 'tests/data/Calculation/Financial/DISC.php';
    }
}
