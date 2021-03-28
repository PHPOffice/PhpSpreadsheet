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
        var_dump($probability, $degrees, $expectedResult);
        $result = Statistical\Distributions\ChiSquared::inverseLeftTail($probability, $degrees);
        var_dump($result);
        if (!is_string($expectedResult)) {
            $reverse = Statistical\Distributions\ChiSquared::distributionLeftTail($result, $degrees, true);
            var_dump($reverse);
            self::assertEqualsWithDelta($probability, $reverse, 1E-6);
        }
        self::assertEqualsWithDelta($expectedResult, $result, 1E-6);
    }

    public function providerCHIINV()
    {
        return require 'tests/data/Calculation/Statistical/CHIINVLeftTail.php';
    }
}
