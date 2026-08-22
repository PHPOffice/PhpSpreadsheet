<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared\Trend;

use PhpOffice\PhpSpreadsheet\Shared\Trend\LinearBestFit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LinearBestFitTest extends TestCase
{
    const LBF_PRECISION = 1.0E-8;

    /**
     * @param array<mixed> $expectedSlope
     * @param array<mixed> $expectedIntersect
     * @param array<mixed> $expectedGoodnessOfFit
     * @param array<float> $yValues
     * @param array<float> $xValues
     */
    #[DataProvider('providerLinearBestFit')]
    public function testLinearBestFit(
        array $expectedSlope,
        array $expectedIntersect,
        array $expectedGoodnessOfFit,
        mixed $expectedEquation,
        array $yValues,
        array $xValues,
        float $xForY,
    ): void {
        $bestFit = new LinearBestFit($yValues, $xValues);
        $slope = $bestFit->getSlope(1);
        self::assertEqualsWithDelta($expectedSlope[0], $slope, self::LBF_PRECISION);
        $slope = $bestFit->getSlope();
        self::assertEqualsWithDelta($expectedSlope[1], $slope, self::LBF_PRECISION);
        $intersect = $bestFit->getIntersect(1);
        self::assertEqualsWithDelta($expectedIntersect[0], $intersect, self::LBF_PRECISION);
        $intersect = $bestFit->getIntersect();
        self::assertEqualsWithDelta($expectedIntersect[1], $intersect, self::LBF_PRECISION);

        $equation = $bestFit->getEquation(2);
        self::assertEquals($expectedEquation, $equation);

        self::assertSame($expectedGoodnessOfFit[0], $bestFit->getGoodnessOfFit(6));
        self::assertSame($expectedGoodnessOfFit[1], $bestFit->getGoodnessOfFit());
        self::assertEqualsWithDelta($xForY, $bestFit->getValueOfXForY(0.0), self::LBF_PRECISION);
    }

    public static function providerLinearBestFit(): array
    {
        return require 'tests/data/Shared/Trend/LinearBestFit.php';
    }

    public function testConstructor(): void
    {
        $bestFit = new LinearBestFit([1, 2, 3], [4, 5]);
        self::assertTrue($bestFit->getError());
        $bestFit = new LinearBestFit([6.0, 8.0, 10.0]);
        self::assertFalse($bestFit->getError());
        self::assertSame([6.0, 8.0, 10.0], $bestFit->getYValues());
        self::assertSame([1.0, 2.0, 3.0], $bestFit->getXValues());
    }
}
