<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared\Trend;

use PhpOffice\PhpSpreadsheet\Shared\Trend\ExponentialBestFit;
use PHPUnit\Framework\TestCase;

class ExponentialBestFitTest extends TestCase
{
    private const EBF_PRECISION6 = 1.0E-6;
    private const EBF_PRECISION5 = 1.0E-5;
    private const DP = 4;

    /**
     * @param array<mixed> $expectedSlope
     * @param array<mixed> $expectedIntersect
     * @param array<mixed> $expectedGoodnessOfFit
     * @param array<float> $yValues
     * @param array<float> $xValues
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('providerExponentialBestFit')]
    public function testExponentialBestFit(
        array $expectedSlope,
        array $expectedIntersect,
        array $expectedGoodnessOfFit,
        mixed $expectedEquation,
        array $yValues,
        array $xValues
    ): void {
        $bestFit = new ExponentialBestFit($yValues, $xValues);
        $slope = $bestFit->getSlope(1);
        self::assertEquals($expectedSlope[0], $slope);

        self::assertFalse($bestFit->getError());
        self::assertEqualsWithDelta(0.2117, $bestFit->getSlopeSE(self::DP), self::EBF_PRECISION5);
        self::assertEqualsWithDelta(1.5380, $bestFit->getIntersectSE(self::DP), self::EBF_PRECISION5);
        self::assertEqualsWithDelta(
            90.486819,
            $bestFit->getGoodnessOfFitPercent(),
            self::EBF_PRECISION6
        );
        self::assertEqualsWithDelta(
            90.4868,
            $bestFit->getGoodnessOfFitPercent(self::DP),
            self::EBF_PRECISION5
        );
        self::assertEqualsWithDelta(2.3031, $bestFit->getStdevOfResiduals(self::DP), self::EBF_PRECISION5);
        self::assertEqualsWithDelta(403.6333, $bestFit->getSSRegression(self::DP), self::EBF_PRECISION5);
        self::assertEqualsWithDelta(42.4353, $bestFit->getSSResiduals(self::DP), self::EBF_PRECISION5);
        self::assertEqualsWithDelta(8, $bestFit->getDFResiduals(self::DP), self::EBF_PRECISION5);
        self::assertEqualsWithDelta(76.0938, $bestFit->getF(self::DP), self::EBF_PRECISION5);
        self::assertEqualsWithDelta(-13.1, $bestFit->getCovariance(self::DP), self::EBF_PRECISION5);
        self::assertEqualsWithDelta(-0.919, $bestFit->getCorrelation(self::DP), self::EBF_PRECISION5);
        self::assertEqualsWithDelta(3.51845, $bestFit->getValueOfXForY(10.0), self::EBF_PRECISION5);
        $values = $bestFit->getYBestFitValues();
        self::assertCount(10, $values);
        self::assertEqualsWithDelta(3.965445, $values[0], self::EBF_PRECISION6);

        $slope = $bestFit->getSlope();
        self::assertEquals($expectedSlope[1], $slope);
        $intersect = $bestFit->getIntersect(1);
        self::assertEquals($expectedIntersect[0], $intersect);
        $intersect = $bestFit->getIntersect();
        self::assertEquals($expectedIntersect[1], $intersect);

        $equation = $bestFit->getEquation(2);
        self::assertEquals($expectedEquation, $equation);

        self::assertSame($expectedGoodnessOfFit[0], $bestFit->getGoodnessOfFit(6));
        self::assertSame($expectedGoodnessOfFit[1], $bestFit->getGoodnessOfFit());
    }

    public static function providerExponentialBestFit(): array
    {
        return require 'tests/data/Shared/Trend/ExponentialBestFit.php';
    }
}
