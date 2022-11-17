<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class YieldMatTest extends TestCase
{
    /**
     * @dataProvider providerYIELDMAT
     *
     * @param mixed $expectedResult
     */
    public function testYIELDMAT($expectedResult, ...$args): void
    {
        $result = Financial\Securities\Yields::yieldAtMaturity(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerYIELDMAT(): array
    {
        return require 'tests/data/Calculation/Financial/YIELDMAT.php';
    }
}
