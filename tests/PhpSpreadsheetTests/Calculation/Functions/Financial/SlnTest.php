<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class SlnTest extends TestCase
{
    /**
     * @dataProvider providerSLN
     *
     * @param mixed $expectedResult
     */
    public function testSLN($expectedResult, array $args): void
    {
        $result = Financial\Depreciation::SLN(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerSLN(): array
    {
        return require 'tests/data/Calculation/Financial/SLN.php';
    }
}
