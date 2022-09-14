<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PHPUnit\Framework\TestCase;

class ErfTest extends TestCase
{
    const ERF_PRECISION = 1E-12;

    /**
     * @dataProvider providerERF
     *
     * @param mixed $lower
     * @param null|mixed $upper
     * @param mixed $expectedResult
     */
    public function testERF($expectedResult, $lower, $upper = null): void
    {
        $result = Engineering::ERF($lower, $upper);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public function providerERF(): array
    {
        return require 'tests/data/Calculation/Engineering/ERF.php';
    }

    /**
     * @dataProvider providerErfArray
     */
    public function testErfArray(array $expectedResult, string $lower, string $upper = 'NULL'): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ERF({$lower}, {$upper})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public function providerErfArray(): array
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
