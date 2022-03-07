<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ErfCTest extends TestCase
{
    const ERF_PRECISION = 1E-12;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerERFC
     *
     * @param mixed $lower
     * @param mixed $expectedResult
     */
    public function testERFC($expectedResult, $lower): void
    {
        $result = Engineering::ERFC($lower);
        self::assertEquals($expectedResult, $result);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public function providerERFC(): array
    {
        return require 'tests/data/Calculation/Engineering/ERFC.php';
    }

    /**
     * @dataProvider providerErfCArray
     */
    public function testErfCArray(array $expectedResult, string $lower): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ERFC({$lower})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public function providerErfCArray(): array
    {
        return [
            'row vector' => [
                [
                    [1.9103139782296354, 1.5204998778130465, 1.0, 0.7236736098317631, 0.0004069520174449588],
                ],
                '{-1.2, -0.5, 0.0, 0.25, 2.5}',
            ],
        ];
    }
}
