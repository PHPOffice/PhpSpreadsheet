<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared\Trend;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\Trend\Trend;
use PHPUnit\Framework\TestCase;

class BestFitTest extends TestCase
{
    private const LBF_PRECISION = 1.0E-4;

    public function testBestFit(): void
    {
        $xValues = [45, 55, 47, 75, 90, 100, 100, 95, 88, 50, 45, 58];
        $yValues = [15, 25, 17, 30, 41, 47, 50, 46, 37, 22, 20, 26];
        $maxGoodness = -1000.0;
        $maxType = '';

        $type = Trend::TREND_LINEAR;
        $result = Trend::calculate($type, $yValues, $xValues);
        $goodness = $result->getGoodnessOfFit();
        if ($maxGoodness < $goodness) {
            $maxGoodness = $goodness;
            $maxType = $type;
        }
        self::assertEqualsWithDelta(0.9628, $goodness, self::LBF_PRECISION);

        $type = Trend::TREND_EXPONENTIAL;
        $result = Trend::calculate($type, $yValues, $xValues);
        $goodness = $result->getGoodnessOfFit();
        if ($maxGoodness < $goodness) {
            $maxGoodness = $goodness;
            $maxType = $type;
        }
        self::assertEqualsWithDelta(0.9952, $goodness, self::LBF_PRECISION);

        $type = Trend::TREND_LOGARITHMIC;
        $result = Trend::calculate($type, $yValues, $xValues);
        $goodness = $result->getGoodnessOfFit();
        if ($maxGoodness < $goodness) {
            $maxGoodness = $goodness;
            $maxType = $type;
        }
        self::assertEqualsWithDelta(-0.0724, $goodness, self::LBF_PRECISION);

        $type = Trend::TREND_POWER;
        $result = Trend::calculate($type, $yValues, $xValues);
        $goodness = $result->getGoodnessOfFit();
        if ($maxGoodness < $goodness) {
            $maxGoodness = $goodness;
            $maxType = $type;
        }
        self::assertEqualsWithDelta(0.9946, $goodness, self::LBF_PRECISION);

        $type = Trend::TREND_BEST_FIT_NO_POLY;
        $result = Trend::calculate($type, $yValues, $xValues);
        $goodness = $result->getGoodnessOfFit();
        self::assertSame($maxGoodness, $goodness);
        self::assertSame(lcfirst($maxType), $result->getBestFitType());

        try {
            $type = Trend::TREND_BEST_FIT;
            Trend::calculate($type, $yValues, [0, 1, 2]);
            self::fail('should have failed - mismatched number of elements');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Number of elements', $e->getMessage());
        }

        try {
            $type = Trend::TREND_BEST_FIT;
            Trend::calculate($type, $yValues, $xValues);
            self::fail('should have failed - TREND_BEST_FIT includes polynomials which are not implemented yet');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('not yet implemented', $e->getMessage());
        }

        try {
            $type = 'unknown';
            Trend::calculate($type, $yValues, $xValues);
            self::fail('should have failed - invalid trend type');
        } catch (SpreadsheetException $e) {
            self::assertStringContainsString('Unknown trend type', $e->getMessage());
        }
    }
}
