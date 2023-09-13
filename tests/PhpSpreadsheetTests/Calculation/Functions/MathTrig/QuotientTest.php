<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class QuotientTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerQUOTIENT
     */
    public function testQUOTIENT(mixed $expectedResult, mixed $arg1 = 'omitted', mixed $arg2 = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        if ($arg1 !== null) {
            $sheet->getCell('A1')->setValue($arg1);
        }
        if ($arg2 !== null) {
            $sheet->getCell('A2')->setValue($arg2);
        }
        if ($arg1 === 'omitted') {
            $sheet->getCell('B1')->setValue('=QUOTIENT()');
        } elseif ($arg2 === 'omitted') {
            $sheet->getCell('B1')->setValue('=QUOTIENT(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=QUOTIENT(A1, A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        self::assertSame($expectedResult, $result);
    }

    public static function providerQUOTIENT(): array
    {
        return require 'tests/data/Calculation/MathTrig/QUOTIENT.php';
    }

    /**
     * @dataProvider providerQuotientArray
     */
    public function testQuotientArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=QUOTIENT({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEqualsWithDelta($expectedResult, $result, 1.0e-14);
    }

    public static function providerQuotientArray(): array
    {
        return [
            'matrix' => [[[3, 3, 2], [2, 2, 1], [1, 0, 0]], '{9, 8, 7; 6, 5, 4; 3, 2, 1}', '2.5'],
        ];
    }
}
