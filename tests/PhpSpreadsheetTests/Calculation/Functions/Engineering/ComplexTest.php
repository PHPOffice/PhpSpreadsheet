<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Complex;
use PHPUnit\Framework\TestCase;

class ComplexTest extends TestCase
{
    /**
     * @dataProvider providerCOMPLEX
     *
     * @param mixed $expectedResult
     */
    public function testCOMPLEX($expectedResult, ...$args): void
    {
        if (count($args) === 0) {
            $result = Complex::complex();
        } elseif (count($args) === 1) {
            $result = Complex::complex($args[0]);
        } elseif (count($args) === 2) {
            $result = Complex::complex($args[0], $args[1]);
        } else {
            $result = Complex::complex($args[0], $args[1], $args[2]);
        }
        self::assertEquals($expectedResult, $result);
    }

    public function providerCOMPLEX(): array
    {
        return require 'tests/data/Calculation/Engineering/COMPLEX.php';
    }

    /**
     * @dataProvider providerComplexArray
     */
    public function testComplexArray(array $expectedResult, string $real, string $imaginary): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=COMPLEX({$real}, {$imaginary})";
        $result = $calculation->calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerComplexArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-2.5-2.5i', '-1-2.5i', '-2.5i', '1-2.5i', '2.5-2.5i'],
                    ['-2.5-i', '-1-i', '-i', '1-i', '2.5-i'],
                    ['-2.5', '-1', '0.0', '1', '2.5'],
                    ['-2.5+i', '-1+i', 'i', '1+i', '2.5+i'],
                    ['-2.5+2.5i', '-1+2.5i', '2.5i', '1+2.5i', '2.5+2.5i'],
                ],
                '{-2.5, -1, 0, 1, 2.5}',
                '{-2.5; -1; 0; 1; 2.5}',
            ],
        ];
    }
}
