<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PHPUnit\Framework\TestCase;

class ErfPreciseTest extends TestCase
{
    const ERF_PRECISION = 1E-12;

    /**
     * @dataProvider providerERFPRECISE
     *
     * @param mixed $limit
     * @param mixed $expectedResult
     */
    public function testERFPRECISE($expectedResult, $limit): void
    {
        $result = Engineering::ERFPRECISE($limit);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public function providerERFPRECISE(): array
    {
        return require 'tests/data/Calculation/Engineering/ERFPRECISE.php';
    }

    /**
     * @dataProvider providerErfPreciseArray
     */
    public function testErfPreciseArray(array $expectedResult, string $limit): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ERF.PRECISE({$limit})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public function providerErfPreciseArray(): array
    {
        return [
            'row vector' => [
                [
                    [-0.9103139782296353, -0.5204998778130465, 0.0, 0.2763263901682369, 0.999593047982555],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
            ],
        ];
    }
}
