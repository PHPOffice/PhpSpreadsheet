<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class BetaDistTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBETADIST
     *
     * @param mixed $expectedResult
     */
    public function testBETADIST($expectedResult, ...$args): void
    {
        $result = Statistical::BETADIST(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerBETADIST(): array
    {
        return require 'tests/data/Calculation/Statistical/BETADIST.php';
    }

    /**
     * @dataProvider providerBetaDistArray
     */
    public function testBetaDistArray(array $expectedResult, string $argument1, string $argument2, string $argument3): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=BETADIST({$argument1}, {$argument2}, {$argument3})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerBetaDistArray(): array
    {
        return [
            'row/column vectors' => [
                [[0.25846539810299873, 0.05696312425682317], [0.3698138247709718, 0.10449584381010533]],
                '0.25',
                '{5, 7.5}',
                '{10; 12}',
            ],
        ];
    }
}
