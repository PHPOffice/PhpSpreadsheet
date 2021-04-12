<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ChiInvLeftTailTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHIINV
     *
     * @param mixed $expectedResult
     * @param mixed $probability
     * @param mixed $degrees
     */
    public function testCHIINV($expectedResult, $probability, $degrees): void
    {
        $result = Statistical\Distributions\ChiSquared::inverseLeftTail($probability, $degrees);
        if (!is_string($expectedResult)) {
            $reverse = Statistical\Distributions\ChiSquared::distributionLeftTail($result, $degrees, true);
            self::assertEqualsWithDelta($probability, $reverse, 1E-12);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCHIINV(): array
    {
        return require 'tests/data/Calculation/Statistical/CHIINVLeftTail.php';
    }
}
