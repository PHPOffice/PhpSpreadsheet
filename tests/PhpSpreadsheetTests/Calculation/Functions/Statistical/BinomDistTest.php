<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BinomDistTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBINOMDIST
     *
     * @param mixed $expectedResult
     */
    public function testBINOMDIST($expectedResult, ...$args): void
    {
        $result = Statistical::BINOMDIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerBINOMDIST(): array
    {
        return require 'tests/data/Calculation/Statistical/BINOMDIST.php';
    }
}
