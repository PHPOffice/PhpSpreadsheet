<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ConfidenceTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCONFIDENCE
     *
     * @param mixed $expectedResult
     */
    public function testCONFIDENCE($expectedResult, ...$args): void
    {
        $result = Statistical::CONFIDENCE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCONFIDENCE(): array
    {
        return require 'tests/data/Calculation/Statistical/CONFIDENCE.php';
    }

    /**
     * @dataProvider providerConfidenceArray
     */
    public function testConfidenceArray(array $expectedResult, string $alpha, string $stdDev, string $size): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=CONFIDENCE({$alpha}, {$stdDev}, {$size})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerConfidenceArray(): array
    {
        return [
            'row/column vectors' => [
                [
                    [0.33261691811208144, 0.6929519127335031, 1.3859038254670062],
                    [0.2351956783344234, 0.48999099653004874, 0.9799819930600975],
                ],
                '0.05',
                '{1.2, 2.5, 5}',
                '{50; 100}',
            ],
        ];
    }
}
