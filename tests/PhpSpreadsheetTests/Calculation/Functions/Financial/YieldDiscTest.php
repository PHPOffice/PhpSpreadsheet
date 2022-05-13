<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Securities\Yields;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class YieldDiscTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerYIELDDISC
     *
     * @param mixed $expectedResult
     */
    public function testYIELDDISC($expectedResult, ...$args): void
    {
        $result = Yields::yieldDiscounted(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerYIELDDISC(): array
    {
        return require 'tests/data/Calculation/Financial/YIELDDISC.php';
    }
}
