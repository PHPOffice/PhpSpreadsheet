<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class RandBetweenTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerRANDBETWEEN
     *
     * @param mixed $expectedResult
     * @param mixed $min
     * @param mixed $max
     */
    public function testRANDBETWEEN($expectedResult, $min = 'omitted', $max = 'omitted'): void
    {
        $this->mightHaveException($expectedResult);
        $sheet = $this->getSheet();
        $lower = (int) $min;
        $upper = (int) $max;
        if ($min !== null) {
            $sheet->getCell('A1')->setValue($min);
        }
        if ($max !== null) {
            $sheet->getCell('A2')->setValue($max);
        }
        if ($min === 'omitted') {
            $sheet->getCell('B1')->setValue('=RANDBETWEEN()');
        } elseif ($max === 'omitted') {
            $sheet->getCell('B1')->setValue('=RANDBETWEEN(A1)');
        } else {
            $sheet->getCell('B1')->setValue('=RANDBETWEEN(A1,A2)');
        }
        $result = $sheet->getCell('B1')->getCalculatedValue();
        if (is_numeric($expectedResult)) {
            self::assertGreaterThanOrEqual($lower, $result);
            self::assertLessThanOrEqual($upper, $result);
        } else {
            self::assertSame($expectedResult, $result);
        }
    }

    public static function providerRANDBETWEEN(): array
    {
        return require 'tests/data/Calculation/MathTrig/RANDBETWEEN.php';
    }

    /**
     * @dataProvider providerRandBetweenArray
     */
    public function testRandBetweenArray(
        int $expectedRows,
        int $expectedColumns,
        string $argument1,
        string $argument2
    ): void {
        $calculation = Calculation::getInstance();

        $formula = "=RandBetween({$argument1}, {$argument2})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertIsArray($result);
        self::assertCount($expectedRows, /** @scrutinizer ignore-type */ $result);
        self::assertIsArray($result[0]);
        self::assertCount($expectedColumns, /** @scrutinizer ignore-type */ $result[0]);
    }

    public static function providerRandBetweenArray(): array
    {
        return [
            'row/column vectors' => [2, 2, '{1, 10}', '{10; 100}'],
        ];
    }
}
