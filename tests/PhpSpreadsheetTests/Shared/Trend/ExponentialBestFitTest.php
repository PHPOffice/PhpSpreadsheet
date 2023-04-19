<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared\Trend;

use PhpOffice\PhpSpreadsheet\Shared\Trend\ExponentialBestFit;
use PHPUnit\Framework\TestCase;

class ExponentialBestFitTest extends TestCase
{
    /**
     * @dataProvider providerExponentialBestFit
     *
     * @param mixed $expectedSlope
     * @param mixed $expectedIntersect
     * @param mixed $expectedGoodnessOfFit
     * @param mixed $yValues
     * @param mixed $xValues
     * @param mixed $expectedEquation
     */
    public function testExponentialBestFit(
        $expectedSlope,
        $expectedIntersect,
        $expectedGoodnessOfFit,
        $expectedEquation,
        $yValues,
        $xValues
    ): void {
        $bestFit = new ExponentialBestFit($yValues, $xValues);
        $slope = $bestFit->getSlope(1);
        self::assertEquals($expectedSlope[0], $slope);
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
