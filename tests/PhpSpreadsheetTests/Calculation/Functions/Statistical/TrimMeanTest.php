<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class TrimMeanTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerTRIMMEAN
     *
     * @param mixed $expectedResult
     * @param mixed $percentage
     */
    public function testTRIMMEAN($expectedResult, array $args, $percentage): void
    {
        $result = Statistical::TRIMMEAN($args, $percentage);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerTRIMMEAN(): array
    {
        return require 'tests/data/Calculation/Statistical/TRIMMEAN.php';
    }
}
