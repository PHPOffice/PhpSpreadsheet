<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Statistical\Distributions;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\GammaBase;
use PHPUnit\Framework\TestCase;

class GammaBaseTest extends TestCase
{
    // incompleteGamma() has no remaining callers in this codebase (it is kept for
    // BC), so exercise it directly against reference values from mpmath's
    // gammainc(a, 0, x), i.e. the unregularized lower incomplete gamma.
    #[\PHPUnit\Framework\Attributes\DataProvider('providerIncompleteGamma')]
    public function testIncompleteGamma(float $expectedResult, float $a, float $x): void
    {
        $result = GammaBase::incompleteGamma($a, $x);
        self::assertEqualsWithDelta($expectedResult, $result, abs($expectedResult) * 1.0e-9);
    }

    public static function providerIncompleteGamma(): array
    {
        return [
            'x < a+1, series branch' => [0.26424111765711536, 2.0, 1.0],
            'integer a, x < a+1' => [0.63212055882855768, 1.0, 1.0],
            'non-integer a' => [1.6918067329451983, 0.5, 2.0],
            'x >= a+1, continued-fraction branch' => [11549.765435275602, 10.0, 5.0],
            'x tiny, series branch' => [1.2640079074328247e-8, 2.5, 0.001],
            // the argument the convergence fix targeted: x far past a+1
            'deep tail, x >> a' => [6.0, 4.0, 80.0],
        ];
    }

    public function testIncompleteGammaNonPositiveInputsReturnZero(): void
    {
        self::assertSame(0.0, GammaBase::incompleteGamma(2.0, 0.0));
        self::assertSame(0.0, GammaBase::incompleteGamma(2.0, -1.0));
        self::assertSame(0.0, GammaBase::incompleteGamma(0.0, 1.0));
    }
}
