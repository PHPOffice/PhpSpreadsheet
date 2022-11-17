<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class YieldDiscTest extends TestCase
{
    /**
     * @dataProvider providerYIELDDISC
     *
     * @param mixed $expectedResult
     */
    public function testYIELDDISC($expectedResult, ...$args): void
    {
        $result = Financial\Securities\Yields::yieldDiscounted(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerYIELDDISC(): array
    {
        return require 'tests/data/Calculation/Financial/YIELDDISC.php';
    }
}
