<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BinomDistRangeTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBINOMDISTRANGE
     *
     * @param mixed $expectedResult
     */
    public function testBINOMDISTRANGE($expectedResult, ...$args): void
    {
        $result = Statistical\Distributions\Binomial::range(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerBINOMDISTRANGE(): array
    {
        return require 'tests/data/Calculation/Statistical/BINOMDISTRANGE.php';
    }
}
