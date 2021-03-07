<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared\Trend;

use PhpOffice\PhpSpreadsheet\Shared\Trend\LogarithmicBestFit;
use PHPUnit\Framework\TestCase;

class LogarithmicBestFitTest extends TestCase
{
    /**
     * @dataProvider providerLogarithmicBestFit
     *
     * @param mixed $expectedSlope
     * @param mixed $expectedIntersect
     * @param mixed $expectedGoodnessOfFit
     * @param mixed $yValues
     * @param mixed $xValues
     * @param mixed $expectedEquation
     */
    public function testLogBestFit(
        $expectedSlope,
        $expectedIntersect,
        $expectedGoodnessOfFit,
        $expectedEquation,
        $yValues,
        $xValues
    ): void {
        $bestFit = new LogarithmicBestFit($yValues, $xValues);
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

    public function providerLogarithmicBestFit()
    {
        return require 'tests/data/Shared/Trend/LogarithmicBestFit.php';
    }
}
