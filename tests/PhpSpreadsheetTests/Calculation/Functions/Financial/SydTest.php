<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class SydTest extends TestCase
{
    /**
     * @dataProvider providerSYD
     *
     * @param mixed $expectedResult
     */
    public function testSYD($expectedResult, array $args): void
    {
        $result = Financial\Depreciation::SYD(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerSYD(): array
    {
        return require 'tests/data/Calculation/Financial/SYD.php';
    }
}
